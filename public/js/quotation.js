let qItems = [];
let qTermins = [];
let editingQuotationId = null;

function parseRupiahStr(rupiahString) {
    if (!rupiahString) return 0;
    return parseInt(rupiahString.toString().replace(/[^,\d]/g, '')) || 0;
}

function formatRupiahInput(element) {
    let value = element.value.replace(/[^,\d]/g, '');
    value = value.replace(/^0+(?=\d)/, ''); // Remove leading zeros
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    element.value = rupiah ? rupiah : '';
}

function addQuotationItem() {
    qItems.push({
        id: Date.now() + Math.random(),
        section: '',
        name: '',
        desc: '',
        qty: 1,
        satuan: 'Paket',
        harga: 0
    });
    renderQuotationItems();
}

function removeQuotationItem(id) {
    qItems = qItems.filter(item => item.id !== id);
    renderQuotationItems();
}

function renderQuotationItems() {
    const container = document.getElementById('q_items_container');
    container.innerHTML = '';
    
    qItems.forEach((item, index) => {
        let subtotal = item.qty * item.harga;
        
        container.innerHTML += `
            <div class="item-card">
                <div class="item-row">
                    <div class="form-group">
                        <label class="form-label">Section (opsional)</label>
                        <input type="text" class="form-control" value="${item.section}" placeholder="Paket Utama / Layanan Tambahan" onchange="updateQItem(${item.id}, 'section', this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Layanan / Produk</label>
                        <input type="text" class="form-control" value="${item.name}" onchange="updateQItem(${item.id}, 'name', this.value)" placeholder="Contoh: Makeup Akad + Resepsi">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi Detail</label>
                    <textarea class="form-control" onchange="updateQItem(${item.id}, 'desc', this.value)" placeholder="Rincian layanan yang didapatkan klien">${item.desc}</textarea>
                </div>
                <div class="item-calc-row" style="margin-top: 8px;">
                    <div class="form-group">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" value="${item.qty}" onchange="updateQItem(${item.id}, 'qty', this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <select class="form-control" onchange="updateQItem(${item.id}, 'satuan', this.value)">
                            <option value="Paket" ${item.satuan === 'Paket' ? 'selected' : ''}>Paket</option>
                            <option value="Pax" ${item.satuan === 'Pax' ? 'selected' : ''}>Pax</option>
                            <option value="Orang" ${item.satuan === 'Orang' ? 'selected' : ''}>Orang</option>
                            <option value="Set" ${item.satuan === 'Set' ? 'selected' : ''}>Set</option>
                            <option value="Hari" ${item.satuan === 'Hari' ? 'selected' : ''}>Hari</option>
                            <option value="Sesi" ${item.satuan === 'Sesi' ? 'selected' : ''}>Sesi</option>
                            <option value="Jam" ${item.satuan === 'Jam' ? 'selected' : ''}>Jam</option>
                            <option value="Pcs" ${item.satuan === 'Pcs' ? 'selected' : ''}>Pcs</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Satuan</label>
                        <input type="text" class="form-control" value="${item.harga ? item.harga.toLocaleString('id-ID') : ''}" placeholder="0" onkeyup="formatRupiahInput(this); updateQItem(${item.id}, 'harga', this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control" id="q_item_subtotal_${item.id}" value="Rp ${subtotal.toLocaleString('id-ID')}" readonly style="background: #f1f1f4; border: none;">
                    </div>
                    <button class="icon-btn" style="margin-top: 24px;" onclick="removeQuotationItem(${item.id})"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            </div>
        `;
    });
    
    calculateQuotationTotals();
}

function updateQItem(id, field, value) {
    let item = qItems.find(x => x.id === id);
    if(item) {
        if(field === 'harga') item[field] = parseRupiahStr(value);
        else item[field] = value;
    }
    
    if (field === 'qty' || field === 'harga') {
        let subtotal = item.qty * item.harga;
        let subElement = document.getElementById('q_item_subtotal_' + id);
        if (subElement) subElement.value = 'Rp ' + subtotal.toLocaleString('id-ID');
        
        calculateQuotationTotals();
    }
}

// Termin Logic
function addTerminItem() {
    qTermins.push({
        id: Date.now() + Math.random(),
        name: 'Termin Baru',
        type: 'Persentase (%)',
        value: 0
    });
    renderTerminItems();
}

function removeTerminItem(id) {
    qTermins = qTermins.filter(item => item.id !== id);
    renderTerminItems();
}

function renderTerminItems() {
    const container = document.getElementById('q_termins_container');
    container.innerHTML = '';
    
    let grandTotal = parseRupiahStr(document.getElementById('q_sum_grandtotal').innerText.replace('Rp ', ''));
    
    qTermins.forEach((termin) => {
        let terminValueRp = termin.type === 'Persentase (%)' 
            ? (grandTotal * (termin.value / 100)) 
            : termin.value;
            
        container.innerHTML += `
            <div class="termin-row">
                <input type="text" class="form-control" value="${termin.name}" onchange="updateQTermin(${termin.id}, 'name', this.value)">
                <select class="form-control" onchange="updateQTermin(${termin.id}, 'type', this.value)">
                    <option value="Persentase (%)" ${termin.type === 'Persentase (%)' ? 'selected' : ''}>Persentase (%)</option>
                    <option value="Nominal (Rp)" ${termin.type === 'Nominal (Rp)' ? 'selected' : ''}>Nominal (Rp)</option>
                </select>
                <input type="number" class="form-control" value="${termin.value || ''}" placeholder="0" onchange="updateQTermin(${termin.id}, 'value', this.value)">
                <button class="icon-btn" onclick="removeTerminItem(${termin.id})"><i class="fa-solid fa-trash-can"></i></button>
            </div>
            <div class="termin-value">Nilai termin: Rp ${terminValueRp.toLocaleString('id-ID')}</div>
        `;
    });
}

function updateQTermin(id, field, value) {
    let item = qTermins.find(x => x.id === id);
    if(item) {
        if(field === 'value') item[field] = parseFloat(value) || 0;
        else item[field] = value;
    }
    renderTerminItems();
}

function calculateQuotationTotals() {
    let subtotal = 0;
    qItems.forEach(item => {
        subtotal += (item.qty * item.harga);
    });
    
    let discount = parseRupiahStr(document.getElementById('q_discount').value);
    let grandTotal = subtotal - discount;
    
    document.getElementById('q_sum_subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('q_sum_discount').innerText = '- Rp ' + discount.toLocaleString('id-ID');
    document.getElementById('q_sum_grandtotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    
    renderTerminItems(); // update termin values
}

function openQuotationModal(id = null) {
    editingQuotationId = id;
    
    if (id) {
        // Load existing
        let quotes = globalQuotations;
        let q = quotes.find(x => x.id === id);
        if(q) {
            document.getElementById('q_no').value = q.q_no;
            document.getElementById('q_status').value = q.status;
            document.getElementById('q_client').value = q.client;
            document.getElementById('q_phone').value = q.phone;
            document.getElementById('q_event_date').value = q.event_date ? q.event_date.substring(0, 10) : '';
            document.getElementById('q_venue').value = q.venue;
            document.getElementById('q_valid_until').value = q.valid_until ? q.valid_until.substring(0, 10) : '';
            document.getElementById('q_discount').value = q.discount ? q.discount.toLocaleString('id-ID') : '';
            document.getElementById('q_tnc').value = q.tnc;
            document.getElementById('q_internal_notes').value = q.internal_notes;
            
            qItems = q.items || [];
            qTermins = q.termins || [];
        }
    } else {
        // New
        let now = new Date();
        document.getElementById('q_no').value = 'QT-' + now.getFullYear() + (now.getMonth()+1).toString().padStart(2,'0') + now.getDate().toString().padStart(2,'0') + '-' + Math.floor(Math.random() * 1000);
        document.getElementById('q_status').value = 'Draft';
        document.getElementById('q_client').value = '';
        document.getElementById('q_phone').value = '';
        document.getElementById('q_event_date').value = '';
        document.getElementById('q_venue').value = '';
        document.getElementById('q_valid_until').value = '';
        document.getElementById('q_discount').value = '';
        document.getElementById('q_internal_notes').value = '';
        
        qItems = [];
        qTermins = [];
        
        // Add 1 default item
        addQuotationItem();
        
        // Add default termins
        qTermins = [
            { id: 1, name: 'DP 1 (Booking Fee)', type: 'Persentase (%)', value: 30 },
            { id: 2, name: 'Pelunasan', type: 'Persentase (%)', value: 70 }
        ];
    }
    
    renderQuotationItems(); // Will also call calculate totals & termins
    
    document.getElementById('quotation-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

let globalQuotations = []; // Store fetched quotations

async function loadQuotations() {
    try {
        let res = await fetch('/api/quotations');
        let rawData = await res.json();
        const parseQuotes = (data) => data.map(q => {
            q.discount = parseFloat(q.discount) || 0;
            q.grandTotal = parseFloat(q.grandTotal) || 0;
            if (q.items) {
                q.items = q.items.map(i => ({...i, harga: parseFloat(i.harga) || 0, qty: parseFloat(i.qty) || 0}));
            }
            if (q.termins) {
                q.termins = q.termins.map(t => ({...t, value: parseFloat(t.value) || 0}));
            }
            return q;
        });
        
        globalQuotations = parseQuotes(rawData);
        
        // Sync local storage if any
        let local = JSON.parse(localStorage.getItem('q_quotations')) || [];
        if (local.length > 0) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            for (let q of local) {
                await fetch('/api/quotations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(q)
                });
            }
            localStorage.removeItem('q_quotations');
            let res2 = await fetch('/api/quotations');
            globalQuotations = parseQuotes(await res2.json());
        }
        
        renderQuotationsList();
    } catch(e) {
        console.error(e);
    }
}

function saveQuotation() {
    let clientName = document.getElementById('q_client').value;
    if (!clientName) {
        alert("Nama klien wajib diisi!");
        return;
    }
    if (qItems.length === 0) {
        alert("Minimal 1 item penawaran wajib ada!");
        return;
    }
    
    let subtotal = parseRupiahStr(document.getElementById('q_sum_subtotal').innerText.replace('Rp ', ''));
    let discount = parseRupiahStr(document.getElementById('q_discount').value);
    let grandTotal = subtotal - discount;

    let payload = {
        id: editingQuotationId || '',
        q_no: document.getElementById('q_no').value,
        status: document.getElementById('q_status').value,
        client: clientName,
        phone: document.getElementById('q_phone').value,
        event_date: document.getElementById('q_event_date').value,
        venue: document.getElementById('q_venue').value,
        valid_until: document.getElementById('q_valid_until').value,
        discount: discount,
        grandTotal: grandTotal,
        tnc: document.getElementById('q_tnc').value,
        internal_notes: document.getElementById('q_internal_notes').value,
        items: qItems,
        termins: qTermins,
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/api/quotations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    }).then(async r => {
        if (!r.ok) throw await r.text();
        return r.json();
    }).then(data => {
        // Log Activity
        if(window.logSysActivity && !editingQuotationId) {
            window.logSysActivity('Lead', 'Penapict', 'Membuat penawaran baru', 'Penawaran untuk: ' + clientName + ' (' + payload.q_no + ')', 'fa-file-contract', 'purple');
        }
        
        if (window.showToast) {
            window.showToast('Penawaran berhasil disimpan!', 'fa-check-circle');
        } else {
            alert("Penawaran berhasil disimpan!");
        }
        
        document.getElementById('quotation-modal').classList.remove('show');
        document.body.style.overflow = '';
        
        loadQuotations();
    }).catch(err => {
        alert("Gagal menyimpan penawaran");
        console.error(err);
    });
}

let currentFilter = 'Semua';
let searchQuery = '';

function setFilter(status, el) {
    currentFilter = status;
    document.querySelectorAll('.tab-item').forEach(tab => tab.classList.remove('active'));
    if(el) el.classList.add('active');
    renderQuotationsList();
}

function handleSearch(query) {
    searchQuery = query.toLowerCase();
    renderQuotationsList();
}

function deleteQuotation(id) {
    if(!confirm('Yakin ingin menghapus penawaran ini?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/api/quotations/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
        .then(() => loadQuotations())
        .catch(e => console.error(e));
}

function renderQuotationsList() {
    let quotes = globalQuotations;
    const container = document.getElementById('quotations-table-body'); // now used as cards container
    const tableContainer = document.getElementById('quotations-table-container');
    const emptyState = document.getElementById('empty-state');
    
    if(!container || !tableContainer || !emptyState) return;
    
    // Filter
    if(currentFilter !== 'Semua') {
        quotes = quotes.filter(q => q.status === currentFilter);
    }
    
    // Search
    if(searchQuery) {
        quotes = quotes.filter(q => 
            (q.client || '').toLowerCase().includes(searchQuery) ||
            (q.q_no || '').toLowerCase().includes(searchQuery)
        );
    }
    
    if (quotes.length === 0) {
        tableContainer.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    tableContainer.style.display = 'block';
    emptyState.style.display = 'none';
    
    container.innerHTML = '';
    quotes.sort((a,b) => new Date(b.created_at) - new Date(a.created_at)).forEach(q => {
        let statusClass = 'status-draft';
        if(q.status === 'Terkirim') statusClass = 'status-terkirim';
        if(q.status === 'Disetujui') statusClass = 'status-disetujui';
        if(q.status === 'Ditolak') statusClass = 'status-ditolak';
        
        let validDate = q.valid_until ? new Date(q.valid_until).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';
        let eventDateStr = q.event_date ? new Date(q.event_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '';
        let venueStr = q.venue ? ' · ' + q.venue : '';
        
        container.innerHTML += `
            <div class="card" style="padding: 16px; margin-bottom: 12px; border: 1px solid var(--border-color); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; background: white;">
                <div>
                    <div style="font-weight: 600; font-size: 15px; margin-bottom: 6px; color: var(--text-dark); display: flex; align-items: center;">
                        ${q.client} <span class="status-badge ${statusClass}" style="font-size: 10px; margin-left: 8px;">${q.status}</span>
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">
                        ${q.q_no} · ${eventDateStr}${venueStr}
                    </div>
                    <div style="font-weight: 700; font-size: 15px; color: var(--text-dark);">
                        Rp ${q.grandTotal.toLocaleString('id-ID')}
                    </div>
                </div>
                <div class="action-btns" style="display: flex; gap: 8px;">
                    <button class="btn btn-outline btn-small" onclick="copyQuotationLink('${q.id}')"><i class="fa-regular fa-copy"></i> Copy Link</button>
                    <button class="btn btn-outline btn-small" onclick="openPreviewModal('${q.id}')"><i class="fa-solid fa-download"></i> Preview & PDF</button>
                    <button class="btn btn-outline btn-small" onclick="openQuotationModal('${q.id}')"><i class="fa-solid fa-pen"></i> Edit</button>
                    <button class="btn btn-outline btn-small" onclick="window.convertToBooking('${q.id}')"><i class="fa-regular fa-calendar-check"></i> Convert to Booking</button>
                    <button class="btn-icon btn-del" style="height: 32px; width: 32px;" title="Hapus" onclick="deleteQuotation('${q.id}')"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            </div>
        `;
    });
}

function copyQuotationLink(id) {
    let themeParam = 'minimal';
    const doc = document.getElementById('preview-document');
    if (doc) {
        if (doc.classList.contains('theme-wedding')) themeParam = 'elegance';
        else if (doc.classList.contains('theme-navy')) themeParam = 'navy';
    }

    const url = window.location.origin + '/q/' + id + '?t=' + themeParam;
    navigator.clipboard.writeText(url).then(() => {
        if(window.showToast) window.showToast('Link berhasil disalin!');
        else alert('Link berhasil disalin!');
    });
}

// Global Show Toast
window.showToast = function(message, iconClass = 'fa-check-circle') {
    const container = document.getElementById('toast-container');
    if(!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = '<i class="fa-solid ' + iconClass + '"></i> ' + message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Initial Render
document.addEventListener('DOMContentLoaded', () => {
    loadQuotations();
    
    // Search listener
    const searchInput = document.querySelector('.search-box input');
    if(searchInput) {
        searchInput.addEventListener('input', (e) => handleSearch(e.target.value));
    }
});

// PREVIEW MODAL LOGIC
let currentPreviewId = null;

function openPreviewModal(id) {
    let quotes = globalQuotations;
    let q = quotes.find(x => x.id === id);
    if(!q) return;
    
    currentPreviewId = id;
    
    // Vendor logic
    let vName = typeof VENDOR_NAME !== 'undefined' ? VENDOR_NAME : 'Vendor';
    document.getElementById('doc_vendor_name').innerText = vName;
    document.getElementById('doc_status').innerText = q.status;
    document.getElementById('doc_no').innerText = 'No. ' + q.q_no;
    
    let validDate = q.valid_until ? new Date(q.valid_until).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';
    let createDate = q.created_at ? new Date(q.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';
    document.getElementById('doc_dates').innerText = 'Terbit: ' + createDate + ' · Berlaku s/d ' + validDate;
    
    document.getElementById('doc_client_name').innerText = q.client || '-';
    document.getElementById('doc_client_phone').innerText = q.phone || '-';
    document.getElementById('doc_event_date').innerText = q.event_date ? new Date(q.event_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';
    document.getElementById('doc_venue').innerText = q.venue || '-';
    
    // Items
    let itemsHtml = '';
    let sections = {};
    (q.items || []).forEach(item => {
        let sec = item.section || 'LAIN-LAIN';
        if(!sections[sec]) sections[sec] = [];
        sections[sec].push(item);
    });
    
    for(let sec in sections) {
        itemsHtml += `<tr><td colspan="4" class="doc-section-title">${sec}</td></tr>`;
        sections[sec].forEach(item => {
            let subtotal = item.qty * item.harga;
            itemsHtml += `
                <tr>
                    <td>
                        <div style="font-weight: 600;">${item.name}</div>
                        <div style="font-size: 11px; color: #666; margin-top: 4px;">${item.desc.replace(/\\n/g, '<br>')}</div>
                    </td>
                    <td style="text-align: center;">${item.qty} ${item.satuan}</td>
                    <td style="text-align: right;">Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td style="text-align: right; font-weight: 600;">Rp ${subtotal.toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
    }
    document.getElementById('doc_items_tbody').innerHTML = itemsHtml;
    
    // Totals
    let subtotal = 0;
    (q.items || []).forEach(item => subtotal += (item.qty * item.harga));
    document.getElementById('doc_subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('doc_discount').innerText = '- Rp ' + (q.discount || 0).toLocaleString('id-ID');
    document.getElementById('doc_grand_total').innerText = 'Rp ' + q.grandTotal.toLocaleString('id-ID');
    
    // Termins
    let terminsHtml = '';
    (q.termins || []).forEach(t => {
        let val = t.type === 'Persentase (%)' ? (q.grandTotal * (t.value / 100)) : t.value;
        let suffix = t.type === 'Persentase (%)' ? ` (${t.value}%)` : '';
        terminsHtml += `
            <div class="doc-termin-card">
                <h5>TAHAP</h5>
                <p>${t.name}${suffix}</p>
                <div class="termin-val">Rp ${val.toLocaleString('id-ID')}</div>
            </div>
        `;
    });
    document.getElementById('doc_termins_grid').innerHTML = terminsHtml;
    
    document.getElementById('doc_tnc_text').innerText = q.tnc || '-';
    document.getElementById('doc_sig_vendor').innerText = vName; // Matching user account profile name!
    document.getElementById('doc_sig_client').innerText = q.client || '-';
    
    const pModal = document.getElementById('preview-modal');
    if(pModal) {
        pModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closePreviewModal() {
    const pModal = document.getElementById('preview-modal');
    if(pModal) {
        pModal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function setDocTheme(themeClass, el) {
    const doc = document.getElementById('preview-document');
    doc.className = 'document-page ' + themeClass;
    
    document.querySelectorAll('.theme-btn').forEach(btn => btn.classList.remove('active'));
    if(el) el.classList.add('active');
}

function downloadDocPNG() {
    const doc = document.getElementById('preview-document');
    if(!doc) return;
    
    // Temporarily adjust styles for capture
    const origWidth = doc.style.width;
    const origMaxWidth = doc.style.maxWidth;
    const origHeight = doc.style.height;
    const origZoom = doc.style.zoom;
    
    // Force exact A4 sizes
    doc.style.width = '794px';
    doc.style.maxWidth = 'none';
    doc.style.height = '1123px';
    doc.style.zoom = '1';
    
    window.showToast('Menyiapkan gambar...', 'fa-spinner fa-spin');
    
    html2canvas(doc, { scale: 2, useCORS: true }).then(canvas => {
        // Restore
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;
        
        let link = document.createElement('a');
        link.download = 'Quotation-' + Date.now() + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        window.showToast('Gambar berhasil diunduh!');
    }).catch(err => {
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;
        console.error(err);
        alert('Gagal mendownload PNG.');
    });
}

function downloadDocPDF() {
    const doc = document.getElementById('preview-document');
    if(!doc || !window.jspdf) return;
    
    // Temporarily adjust styles for capture
    const origWidth = doc.style.width;
    const origMaxWidth = doc.style.maxWidth;
    const origHeight = doc.style.height;
    const origZoom = doc.style.zoom;
    
    // Force exact A4 sizes
    doc.style.width = '794px';
    doc.style.maxWidth = 'none';
    doc.style.height = '1123px';
    doc.style.zoom = '1';

    window.showToast('Menyiapkan PDF...', 'fa-spinner fa-spin');
    
    html2canvas(doc, { scale: 2, useCORS: true }).then(canvas => {
        // Restore
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;
        
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
        
        // Exact 1-page A4 dimensions (210mm x 297mm)
        const pdfWidth = 210;
        const pdfHeight = 297;
        
        pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
        pdf.save('Quotation-' + Date.now() + '.pdf');
        window.showToast('PDF berhasil diunduh!');
    }).catch(err => {
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;
        console.error(err);
        alert('Gagal mendownload PDF.');
    });
}

window.convertToBooking = async function(id) {
    if(!confirm('Convert penawaran ini ke Booking?')) return;
    
    const q = globalQuotations.find(x => x.id === id);
    if (!q) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // 1. Update quotation status to 'Disetujui'
    const qPayload = { ...q, status: 'Disetujui' };
    try {
        await fetch('/api/quotations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(qPayload)
        });
        
        // 2. Prepare Booking Payload
        let packageName = '';
        let packagePrice = 0;
        let packageQty = 1;
        let addonsData = [];
        
        if (q.items && q.items.length > 0) {
            packageName = q.items[0].name || '';
            packagePrice = parseFloat(q.items[0].price) || 0;
            packageQty = parseFloat(q.items[0].qty) || 1;
            
            if (q.items.length > 1) {
                addonsData = q.items.slice(1).map(item => ({
                    name: item.name || '',
                    qty: item.qty || 1,
                    price: item.price || 0
                }));
            }
        }
        
        const totalPackage = packagePrice * packageQty;
        const totalAddon = addonsData.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1)), 0);
        const discount = parseFloat(q.discount) || 0;
        const totalIncome = totalPackage + totalAddon - discount;
        
        const bookingPayload = {
            client_name: q.client || '',
            client_wa_number: q.phone || '',
            client_address: q.client_address || '',
            event_date: q.event_date || '',
            start_time: q.event_time || '',
            end_time: '',
            package_name: packageName,
            package_price: packagePrice,
            package_qty: packageQty,
            paid_amount: 0,
            discount: discount,
            addons: addonsData,
            operational_costs: [],
            total_income: totalIncome,
            total_operational_cost: 0,
            net_profit: totalIncome,
            payment_date: '',
            payment_status: 'DP 1',
            production_status: 'Pre-Event',
            result_link: '',
            team_members: [],
            notes: q.notes || ''
        };
        
        // 3. Post to Bookings
        const resBooking = await fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(bookingPayload)
        });
        
        if (window.showToast) {
            window.showToast('Penawaran diubah menjadi booking');
        } else {
            alert('Penawaran diubah menjadi booking');
        }
        
        // Reload quotations
        loadQuotations();
        
    } catch (e) {
        console.error(e);
        alert('Gagal mengconvert ke booking.');
    }
}

