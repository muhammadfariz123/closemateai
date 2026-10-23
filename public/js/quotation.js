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
        let quotes = globalQuotations || [];
        let q = quotes.find(x => String(x.id) === String(id) || x.id == id);
        if(q) {
            document.getElementById('q_no').value = q.q_no || '';
            document.getElementById('q_status').value = q.status || 'Draft';
            document.getElementById('q_client').value = q.client || '';
            document.getElementById('q_phone').value = q.phone || '';
            document.getElementById('q_event_date').value = q.event_date ? q.event_date.substring(0, 10) : '';
            document.getElementById('q_venue').value = q.venue || '';
            document.getElementById('q_valid_until').value = q.valid_until ? q.valid_until.substring(0, 10) : '';
            document.getElementById('q_discount').value = q.discount ? q.discount.toLocaleString('id-ID') : '';
            document.getElementById('q_tnc').value = q.tnc || '';
            document.getElementById('q_internal_notes').value = q.internal_notes || '';
            
            qItems = Array.isArray(q.items) ? JSON.parse(JSON.stringify(q.items)) : [];
            qTermins = Array.isArray(q.termins) ? JSON.parse(JSON.stringify(q.termins)) : [];
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
        const parseQuotes = (data) => (Array.isArray(data) ? data : []).map(q => {
            q.discount = parseFloat(q.discount) || 0;
            q.grandTotal = parseFloat(q.grandTotal) || 0;
            if (typeof q.items === 'string') {
                try { q.items = JSON.parse(q.items); } catch(e) { q.items = []; }
            }
            if (typeof q.termins === 'string') {
                try { q.termins = JSON.parse(q.termins); } catch(e) { q.termins = []; }
            }
            if (Array.isArray(q.items)) {
                q.items = q.items.map(i => ({
                    ...i,
                    harga: parseFloat(i.harga) || 0,
                    qty: parseFloat(i.qty) || 0,
                    desc: i.desc || '',
                    name: i.name || '',
                    section: i.section || '',
                    satuan: i.satuan || 'Paket'
                }));
            } else {
                q.items = [];
            }
            if (Array.isArray(q.termins)) {
                q.termins = q.termins.map(t => ({
                    ...t,
                    name: t.name || '',
                    type: t.type || 'Persentase (%)',
                    value: parseFloat(t.value) || 0
                }));
            } else {
                q.termins = [];
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

// Helper: Indonesian Date Formatter
function formatIndonesianDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const str = String(dateStr).trim();
        const match = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if (match) {
            const year = match[1];
            const month = months[parseInt(match[2], 10) - 1] || match[2];
            const day = parseInt(match[3], 10);
            return `${day} ${month} ${year}`;
        }
        const d = new Date(str);
        if (!isNaN(d.getTime())) {
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
        return str;
    } catch (e) {
        return String(dateStr);
    }
}

// PREVIEW MODAL LOGIC
let currentPreviewId = null;

function populatePreviewDocument(q) {
    if (!q) return;

    // Vendor info
    let vName = (typeof VENDOR_NAME !== 'undefined' && VENDOR_NAME) ? VENDOR_NAME : 'Penapict';
    const elVendor = document.getElementById('doc_vendor_name');
    if (elVendor) elVendor.innerText = vName;
    
    const elSigVendor = document.getElementById('doc_sig_vendor');
    if (elSigVendor) elSigVendor.innerText = vName;

    // Client & quotation metadata
    const clientName = q.client || '-';
    const elClient = document.getElementById('doc_client_name');
    if (elClient) elClient.innerText = clientName;

    const elSigClient = document.getElementById('doc_sig_client');
    if (elSigClient) elSigClient.innerText = clientName;

    const elStatus = document.getElementById('doc_status');
    if (elStatus) elStatus.innerText = q.status || 'Draft';

    const elNo = document.getElementById('doc_no');
    if (elNo) elNo.innerText = 'No. ' + (q.q_no || 'QT-0000');

    let validDate = formatIndonesianDate(q.valid_until);
    let createDate = formatIndonesianDate(q.created_at || new Date());
    const elDates = document.getElementById('doc_dates');
    if (elDates) elDates.innerText = 'Terbit: ' + createDate + ' · Berlaku s/d ' + validDate;

    const elPhone = document.getElementById('doc_client_phone');
    if (elPhone) elPhone.innerText = q.phone || '-';

    const elEventDate = document.getElementById('doc_event_date');
    if (elEventDate) elEventDate.innerText = formatIndonesianDate(q.event_date);

    const elVenue = document.getElementById('doc_venue');
    if (elVenue) elVenue.innerText = q.venue || '-';

    // Render items grouped by section
    let itemsHtml = '';
    let sections = {};
    const rawItems = Array.isArray(q.items) ? q.items : [];

    rawItems.forEach(item => {
        let sec = (item.section && String(item.section).trim()) ? String(item.section).trim() : 'PAKET UTAMA';
        if (!sections[sec]) sections[sec] = [];
        sections[sec].push(item);
    });

    // Fallback if no items
    if (Object.keys(sections).length === 0) {
        sections['PAKET UTAMA'] = [{
            name: 'Layanan / Produk',
            desc: '',
            qty: 1,
            satuan: 'Paket',
            harga: 0
        }];
    }

    for (let sec in sections) {
        itemsHtml += `<tr><td colspan="4" class="doc-section-title">${sec}</td></tr>`;
        sections[sec].forEach(item => {
            let qty = parseFloat(item.qty) || 1;
            let harga = parseFloat(item.harga) || 0;
            let subtotal = qty * harga;
            let descText = (item.desc !== null && item.desc !== undefined) ? String(item.desc).trim() : '';
            let descHtml = descText 
                ? `<div style="font-size: 11px; color: #666; margin-top: 4px; line-height: 1.4;">${descText.replace(/\\n/g, '<br>').replace(/\n/g, '<br>')}</div>` 
                : '';

            itemsHtml += `
                <tr>
                    <td>
                        <div style="font-weight: 600;">${item.name || 'Layanan / Produk'}</div>
                        ${descHtml}
                    </td>
                    <td style="text-align: center;">${qty} ${item.satuan || 'Paket'}</td>
                    <td style="text-align: right;">Rp ${Math.round(harga).toLocaleString('id-ID')}</td>
                    <td style="text-align: right; font-weight: 600;">Rp ${Math.round(subtotal).toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
    }

    const elItemsTbody = document.getElementById('doc_items_tbody');
    if (elItemsTbody) elItemsTbody.innerHTML = itemsHtml;

    // Totals
    let calcSubtotal = 0;
    rawItems.forEach(item => {
        let iQty = parseFloat(item.qty) || 0;
        let iHarga = parseFloat(item.harga) || 0;
        calcSubtotal += (iQty * iHarga);
    });

    let discount = parseFloat(q.discount) || 0;
    let grandTotal = parseFloat(q.grandTotal);
    if (isNaN(grandTotal) || grandTotal === null || grandTotal === undefined) {
        grandTotal = Math.max(0, calcSubtotal - discount);
    }

    const elSub = document.getElementById('doc_subtotal');
    if (elSub) elSub.innerText = 'Rp ' + Math.round(calcSubtotal).toLocaleString('id-ID');

    const elDisc = document.getElementById('doc_discount');
    if (elDisc) elDisc.innerText = '- Rp ' + Math.round(discount).toLocaleString('id-ID');

    const elGrand = document.getElementById('doc_grand_total');
    if (elGrand) elGrand.innerText = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');

    // Termins
    let terminsHtml = '';
    const rawTermins = Array.isArray(q.termins) ? q.termins : [];

    rawTermins.forEach((t, idx) => {
        let tVal = parseFloat(t.value) || 0;
        let isPercent = t.type === 'Persentase (%)';
        let nominal = isPercent ? (grandTotal * (tVal / 100)) : tVal;
        let suffix = (isPercent && !String(t.name || '').includes('%')) ? ` (${tVal}%)` : '';

        terminsHtml += `
            <div class="doc-termin-card">
                <h5>TAHAP ${idx + 1}</h5>
                <p>${t.name || ('Tahap ' + (idx + 1))}${suffix}</p>
                <div class="termin-val">Rp ${Math.round(nominal).toLocaleString('id-ID')}</div>
            </div>
        `;
    });

    const elTermins = document.getElementById('doc_termins_grid');
    if (elTermins) elTermins.innerHTML = terminsHtml;

    // T&C
    const elTnc = document.getElementById('doc_tnc_text');
    if (elTnc) elTnc.innerText = q.tnc || '-';
}

function openPreviewModal(id) {
    let quotes = globalQuotations || [];
    let q = quotes.find(x => String(x.id) === String(id) || x.id == id);
    if (!q) {
        console.warn("Quotation not found with id:", id);
        return;
    }

    currentPreviewId = id;
    populatePreviewDocument(q);

    const pModal = document.getElementById('preview-modal');
    if (pModal) {
        pModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function previewCurrentForm() {
    let subtotal = parseRupiahStr(document.getElementById('q_sum_subtotal')?.innerText?.replace('Rp ', '') || '0');
    let discount = parseRupiahStr(document.getElementById('q_discount')?.value || '0');
    let grandTotal = subtotal - discount;

    let currentItems = (qItems && qItems.length > 0) ? JSON.parse(JSON.stringify(qItems)) : [];
    let currentTermins = (qTermins && qTermins.length > 0) ? JSON.parse(JSON.stringify(qTermins)) : [];

    let formQuotation = {
        id: editingQuotationId || 'temp',
        q_no: document.getElementById('q_no')?.value || 'QT-DRAFT',
        status: document.getElementById('q_status')?.value || 'Draft',
        client: document.getElementById('q_client')?.value || 'Nama Klien',
        phone: document.getElementById('q_phone')?.value || '',
        event_date: document.getElementById('q_event_date')?.value || '',
        venue: document.getElementById('q_venue')?.value || '',
        valid_until: document.getElementById('q_valid_until')?.value || '',
        discount: discount,
        grandTotal: grandTotal,
        tnc: document.getElementById('q_tnc')?.value || '',
        internal_notes: document.getElementById('q_internal_notes')?.value || '',
        items: currentItems.length > 0 ? currentItems : [{
            section: 'PAKET UTAMA',
            name: 'Layanan / Produk',
            desc: '',
            qty: 1,
            satuan: 'Paket',
            harga: 0
        }],
        termins: currentTermins,
        created_at: new Date().toISOString()
    };

    currentPreviewId = formQuotation.id;
    populatePreviewDocument(formQuotation);

    const pModal = document.getElementById('preview-modal');
    if (pModal) {
        pModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closePreviewModal() {
    const pModal = document.getElementById('preview-modal');
    if (pModal) {
        pModal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function setDocTheme(themeClass, el) {
    const doc = document.getElementById('preview-document');
    if (!doc) return;
    doc.className = 'document-page ' + themeClass;

    document.querySelectorAll('.theme-btn').forEach(btn => btn.classList.remove('active'));
    if (el) {
        el.classList.add('active');
    } else {
        document.querySelectorAll('.theme-btn').forEach(btn => {
            if (btn.getAttribute('onclick')?.includes(themeClass)) {
                btn.classList.add('active');
            }
        });
    }
}

function downloadDocPNG() {
    if (typeof window.downloadDocPNG === 'function' && window.downloadDocPNG !== downloadDocPNG) {
        return window.downloadDocPNG();
    }
    const doc = document.getElementById('preview-document');
    if (!doc || typeof html2canvas === 'undefined') {
        alert('Pustaka gambar belum siap.');
        return;
    }

    const origWidth = doc.style.width;
    const origMaxWidth = doc.style.maxWidth;
    const origHeight = doc.style.height;
    const origZoom = doc.style.zoom;

    doc.style.width = '794px';
    doc.style.maxWidth = 'none';
    doc.style.height = '1123px';
    doc.style.zoom = '1';

    if (window.showToast) window.showToast('Menyiapkan gambar...', 'fa-spinner fa-spin');

    html2canvas(doc, { scale: 2, useCORS: true }).then(canvas => {
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;

        let link = document.createElement('a');
        link.download = 'Quotation-' + Date.now() + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        if (window.showToast) window.showToast('Gambar berhasil diunduh!');
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
    if (typeof window.downloadDocPDF === 'function' && window.downloadDocPDF !== downloadDocPDF) {
        return window.downloadDocPDF();
    }
    const doc = document.getElementById('preview-document');
    let jsPDFClass = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);
    if (!doc || !jsPDFClass || typeof html2canvas === 'undefined') {
        alert('Pustaka PDF belum siap.');
        return;
    }

    const origWidth = doc.style.width;
    const origMaxWidth = doc.style.maxWidth;
    const origHeight = doc.style.height;
    const origZoom = doc.style.zoom;

    doc.style.width = '794px';
    doc.style.maxWidth = 'none';
    doc.style.height = '1123px';
    doc.style.zoom = '1';

    if (window.showToast) window.showToast('Menyiapkan PDF...', 'fa-spinner fa-spin');

    html2canvas(doc, { scale: 2, useCORS: true }).then(canvas => {
        doc.style.width = origWidth;
        doc.style.maxWidth = origMaxWidth;
        doc.style.height = origHeight;
        doc.style.zoom = origZoom;

        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const pdf = new jsPDFClass('p', 'mm', 'a4');
        pdf.addImage(imgData, 'JPEG', 0, 0, 210, 297);
        pdf.save('Quotation-' + Date.now() + '.pdf');
        if (window.showToast) window.showToast('PDF berhasil diunduh!');
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
    if (!confirm('Convert penawaran ini ke Booking?')) return;

    const q = (globalQuotations || []).find(x => String(x.id) === String(id) || x.id == id);
    if (!q) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

        let packageName = 'Penawaran ' + q.q_no;
        let packagePrice = parseFloat(q.grandTotal) || 0;
        let packageQty = 1;

        let venueStr = q.venue || '';
        let notesText = `Dibuat dari penawaran ${q.q_no} \u00B7 Lokasi: ${venueStr}`;

        const bookingPayload = {
            client_name: q.client || '',
            client_wa_number: q.phone || '',
            client_address: venueStr,
            event_date: q.event_date ? q.event_date.substring(0, 10) : '',
            start_time: q.event_time || '',
            end_time: '',
            package_name: packageName,
            package_price: packagePrice,
            package_qty: packageQty,
            paid_amount: 0,
            discount: 0,
            addons: [],
            operational_costs: [],
            total_income: packagePrice,
            total_operational_cost: 0,
            net_profit: packagePrice,
            payment_date: '',
            payment_status: 'DP 1',
            production_status: 'Pre-Event',
            result_link: '',
            team_members: [],
            notes: notesText
        };

        await fetch('/api/bookings', {
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

        loadQuotations();
    } catch (e) {
        console.error(e);
        alert('Gagal mengconvert ke booking.');
    }
};

// Global Exposure for HTML Event Handlers
window.openPreviewModal = openPreviewModal;
window.previewCurrentForm = previewCurrentForm;
window.closePreviewModal = closePreviewModal;
window.setDocTheme = setDocTheme;
window.downloadDocPNG = downloadDocPNG;
window.downloadDocPDF = downloadDocPDF;
window.openQuotationModal = openQuotationModal;
window.addQuotationItem = addQuotationItem;
window.removeQuotationItem = removeQuotationItem;
window.updateQItem = updateQItem;
window.addTerminItem = addTerminItem;
window.removeTerminItem = removeTerminItem;
window.updateQTermin = updateQTermin;
window.calculateQuotationTotals = calculateQuotationTotals;
window.saveQuotation = saveQuotation;
window.copyQuotationLink = copyQuotationLink;
window.deleteQuotation = deleteQuotation;
window.setFilter = setFilter;
window.formatRupiahInput = formatRupiahInput;

