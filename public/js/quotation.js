let qItems = [];
let qTermins = [];
let editingQuotationId = null;

function parseRupiahStr(rupiahString) {
    if (!rupiahString) return 0;
    return parseInt(rupiahString.toString().replace(/[^,\d]/g, '')) || 0;
}

function formatRupiahInput(element) {
    let value = element.value.replace(/[^,\d]/g, '');
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
                        <input type="text" class="form-control" value="${item.section}" onchange="updateQItem(${item.id}, 'section', this.value)">
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
                        <input type="text" class="form-control" value="${item.harga > 0 ? item.harga.toLocaleString('id-ID') : 0}" onkeyup="formatRupiahInput(this); updateQItem(${item.id}, 'harga', this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control" value="Rp ${subtotal.toLocaleString('id-ID')}" readonly style="background: #f1f1f4; border: none;">
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
        renderQuotationItems(); // re-render to update subtotal
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
                <input type="number" class="form-control" value="${termin.value}" onchange="updateQTermin(${termin.id}, 'value', this.value)">
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
        let quotes = JSON.parse(localStorage.getItem('q_quotations')) || [];
        let q = quotes.find(x => x.id === id);
        if(q) {
            document.getElementById('q_no').value = q.q_no;
            document.getElementById('q_status').value = q.status;
            document.getElementById('q_client').value = q.client;
            document.getElementById('q_phone').value = q.phone;
            document.getElementById('q_event_date').value = q.event_date;
            document.getElementById('q_venue').value = q.venue;
            document.getElementById('q_valid_until').value = q.valid_until;
            document.getElementById('q_discount').value = q.discount.toLocaleString('id-ID');
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
        document.getElementById('q_discount').value = '0';
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
        id: editingQuotationId || ('q_' + Date.now()),
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
        created_at: new Date().toISOString()
    };

    let quotes = JSON.parse(localStorage.getItem('q_quotations')) || [];
    
    if (editingQuotationId) {
        const idx = quotes.findIndex(x => x.id === editingQuotationId);
        if (idx > -1) quotes[idx] = payload;
        else quotes.push(payload);
    } else {
        quotes.push(payload);
    }
    
    localStorage.setItem('q_quotations', JSON.stringify(quotes));
    
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
    
    renderQuotationsList();
}

function deleteQuotation(id) {
    if(!confirm('Yakin ingin menghapus penawaran ini?')) return;
    let quotes = JSON.parse(localStorage.getItem('q_quotations')) || [];
    quotes = quotes.filter(q => q.id !== id);
    localStorage.setItem('q_quotations', JSON.stringify(quotes));
    renderQuotationsList();
}

function renderQuotationsList() {
    let quotes = JSON.parse(localStorage.getItem('q_quotations')) || [];
    const tbody = document.getElementById('quotations-table-body');
    const tableContainer = document.getElementById('quotations-table-container');
    const emptyState = document.getElementById('empty-state');
    
    if(!tbody || !tableContainer || !emptyState) return;
    
    if (quotes.length === 0) {
        tableContainer.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    tableContainer.style.display = 'block';
    emptyState.style.display = 'none';
    
    tbody.innerHTML = '';
    quotes.sort((a,b) => new Date(b.created_at) - new Date(a.created_at)).forEach(q => {
        let statusClass = 'status-draft';
        if(q.status === 'Terkirim') statusClass = 'status-terkirim';
        if(q.status === 'Disetujui') statusClass = 'status-disetujui';
        if(q.status === 'Ditolak') statusClass = 'status-ditolak';
        
        let validDate = q.valid_until ? new Date(q.valid_until).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-';
        
        tbody.innerHTML += `
            <tr>
                <td><strong>${q.q_no}</strong></td>
                <td>
                    <div style="font-weight: 600; color: var(--text-dark);">${q.client}</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">${q.event_date ? new Date(q.event_date).toLocaleDateString('id-ID') : 'TBA'}</div>
                </td>
                <td style="font-weight: 600;">Rp ${q.grandTotal.toLocaleString('id-ID')}</td>
                <td>${validDate}</td>
                <td><span class="status-badge ${statusClass}">${q.status}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="btn-icon btn-view" title="Edit/Lihat" onclick="openQuotationModal('${q.id}')"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button class="btn-icon btn-send-wa" title="Kirim WA" onclick="alert('Fitur Kirim WA (Mockup)')"><i class="fa-brands fa-whatsapp"></i></button>
                        <button class="btn-icon btn-del" title="Hapus" onclick="deleteQuotation('${q.id}')"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </td>
            </tr>
        `;
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
    renderQuotationsList();
});
