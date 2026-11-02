    <div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; width: 600px; max-height: 90vh; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 id="booking_modal_title" style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">Tambah Booking</h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Kelola data acara, pembayaran, rincian biaya operasional, dan progres produksi klien Anda.</p>
                </div>
                <button onclick="closeBookingModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div style="padding: 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Nama Klien</label>
                        <input type="text" id="b_client_name" class="form-control" placeholder="Pilih dari lead Booked atau ketik manual" oninput="toggleHandlerBadge()">
                        <div id="b_handler_badge" style="display: none; margin-top: 8px; padding: 4px 10px; background: rgba(107, 92, 216, 0.1); color: #6b5cd8; border-radius: 20px; font-size: 11px;">
                            <i class="fa-regular fa-user"></i> Handler: <span id="b_handler_name">{{ auth()->check() ? auth()->user()->name : 'Penapict' }}</span>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">No. WhatsApp</label>
                        <input type="text" id="b_client_wa" class="form-control" placeholder="628123456789">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Marketing (Closing)</label>
                        <input type="text" id="b_marketing_name" list="marketing_options" class="form-control" placeholder="Danu" value="Danu">
                        <datalist id="marketing_options">
                            <option value="Danu">
                            <option value="Mahkota">
                            <option value="Dina">
                            <option value="Arselia">
                            <option value="Naufal">
                            <option value="Nanang">
                        </datalist>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Alamat Klien (opsional)</label>
                    <textarea id="b_client_address" class="form-control" placeholder="Alamat lengkap klien" rows="2"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Tanggal Acara</label>
                    <input type="date" id="b_event_date" class="form-control">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Jam Mulai (opsional)</label>
                        <input type="time" id="b_start_time" class="form-control">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Jam Selesai (opsional)</label>
                        <input type="time" id="b_end_time" class="form-control">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: start;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Nama Paket</label>
                        <div style="position: relative;">
                            <input type="text" id="b_package_name" class="form-control" placeholder="Pilih dari pricelist atau ketik manual" oninput="checkPackagePrice()" autocomplete="off">
                            <i class="fa-solid fa-caret-down" onclick="togglePackageDropdown(event)" style="position: absolute; right: 12px; top: 12px; color: var(--text-muted); cursor: pointer; pointer-events: auto;"></i>
                            <div id="package_dropdown" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #232328; color: white; border-radius: 8px; margin-top: 4px; z-index: 10; max-height: 200px; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                                <!-- Dropdown items -->
                            </div>
                        </div>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Saran diambil dari paket tersimpan.</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Harga Paket (Rp)</label>
                        <input type="text" id="b_package_price" class="form-control" placeholder="0" oninput="formatRupiahInput(this); calculateBooking()">
                        <button type="button" onclick="savePackageToLocal()" class="btn btn-secondary" style="margin-top: 8px; width: 100%; font-size: 13px; padding: 6px 12px;"><i class="fa-regular fa-floppy-disk"></i> Simpan Paket & Harga</button>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Tersimpan di perangkat ini dan bisa dipakai lagi di Invoice Generator.</p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Jumlah Paket</label>
                        <input type="number" id="b_package_qty" class="form-control" value="1" min="1" oninput="calculateBooking()">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Sudah Dibayar (Rp)</label>
                        <input type="text" id="b_paid_amount" class="form-control" value="0" oninput="formatRupiahInput(this)">
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Diskon (Rp)</label>
                    <input type="text" id="b_discount" class="form-control" value="0" oninput="formatRupiahInput(this); calculateBooking()">
                </div>
                
                <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="font-size: 14px; font-weight: 600;">Add-On Item</h4>
                        <button class="btn btn-secondary" onclick="addBookingAddon()" style="padding: 4px 12px; font-size: 12px;"><i class="fa-solid fa-plus"></i> Tambah Add-On</button>
                    </div>
                    <div id="b_addons_container">
                        <!-- Addon list -->
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--border-color);">
                        <span id="b_label_total_harga_addon" style="font-size: 13px; color: var(--text-muted);">Total Harga (paket + add-on)</span>
                        <strong style="font-size: 14px;" id="b_label_total_income">Rp 0</strong>
                    </div>
                </div>
                
                <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="font-size: 14px; font-weight: 600;">Biaya Operasional (HPP)</h4>
                        <button class="btn btn-secondary" onclick="addBookingCost()" style="padding: 4px 12px; font-size: 12px;"><i class="fa-solid fa-plus"></i> Tambah Biaya</button>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; background: rgba(0,0,0,0.02); padding: 12px; border-radius: 8px;">
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <select id="b_hpp_template" class="form-control" style="flex: 1; min-width: 150px;" onchange="onHppTemplateChange()">
                                <option value="">Belum ada template HPP</option>
                            </select>
                            <button id="btn_apply_hpp_template" class="btn btn-primary" onclick="applyHppTemplate()" style="padding: 6px 12px; font-size: 12px; display: none; background: var(--primary); border: none; border-radius: 6px;">Terapkan Template</button>
                            <button id="btn_delete_hpp_template" onclick="deleteHppTemplate()" style="padding: 6px 12px; font-size: 12px; display: none; background: none; border: none; color: var(--danger); cursor: pointer;"><i class="fa-regular fa-trash-can"></i> Hapus Template</button>
                        </div>
                        <div id="b_hpp_save_container" style="display: flex; gap: 8px; align-items: center; margin-top: 4px;">
                            <button id="btn_show_save_hpp" class="btn btn-secondary" onclick="toggleSaveHppInput()" style="padding: 4px 12px; font-size: 12px; border-radius: 6px;"><i class="fa-regular fa-floppy-disk"></i> Simpan Template</button>
                            <div id="b_hpp_save_input_group" style="display: none; flex: 1; gap: 8px; align-items: center;">
                                <input type="text" id="b_hpp_template_name" class="form-control" placeholder="Nama template HPP baru..." style="flex: 1; font-size: 13px;">
                                <button class="btn btn-primary" onclick="saveHppTemplate()" style="padding: 6px 16px; font-size: 12px; border-radius: 20px;">Simpan</button>
                            </div>
                        </div>
                    </div>
                    <div id="b_costs_container">
                        <!-- Costs list -->
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--border-color);">
                        <span style="font-size: 13px; color: var(--text-muted);">Total Biaya Operasional</span>
                        <strong style="font-size: 14px;" id="b_label_total_cost">Rp 0</strong>
                    </div>
                </div>
                
                <!-- RINCIAN LAPORAN KEUANGAN & KATEGORI PENAPICT (SINKRON KE EXCEL) -->
                <div style="border: 1px solid #bfdbfe; border-radius: 10px; background: #f0f7ff; overflow: hidden;">
                    <div onclick="toggleFinancialSection()" style="padding: 12px 16px; background: #dbeafe; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                        <span style="font-weight: 700; font-size: 13px; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-file-invoice-dollar" style="color: #2563eb; font-size: 15px;"></i>
                            Rincian Kolom Laporan Keuangan &amp; Kategori Paket (Sinkron Excel)
                        </span>
                        <span style="font-size: 11px; color: #3b82f6; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                            <span id="financial_section_status">Buka / Edit Kolom</span>
                            <i id="financial_chevron" class="fa-solid fa-chevron-down" style="transition: transform 0.2s;"></i>
                        </span>
                    </div>
                    <div id="financial_section_body" style="display: none; padding: 16px; flex-direction: column; gap: 16px;">
                        <!-- 1. Kategori Checkbox -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                1. Kategori Layanan (Tampil sebagai Checkbox ☑ di Excel)
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div>
                                    <strong style="font-size: 11px; color: #2563eb; display: block; margin-bottom: 4px;">PHOTO</strong>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_p_b" value="B"> B (Basic)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_p_e" value="E"> E (Exclusive)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_p_wb" value="WB"> WB (Book)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_p_roll" value="ROLL"> ROLL</label>
                                </div>
                                <div>
                                    <strong style="font-size: 11px; color: #2563eb; display: block; margin-bottom: 4px;">CINEMA</strong>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_c_b" value="B"> B (Basic)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_c_e" value="E"> E (Exclusive)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_c_l" value="L"> L (Liputan)</label>
                                </div>
                                <div>
                                    <strong style="font-size: 11px; color: #2563eb; display: block; margin-bottom: 4px;">LIPUTAN</strong>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_l_b" value="B"> B (Basic)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_l_e" value="E"> E (Exclusive)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_l_l" value="L"> L (Liputan)</label>
                                </div>
                                <div>
                                    <strong style="font-size: 11px; color: #2563eb; display: block; margin-bottom: 4px;">KONTEN</strong>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_k_h" value="H"> H (Highlight)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_k_f" value="F"> F (Full)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_k_sde" value="SDE"> SDE (Same Day)</label>
                                </div>
                                <div>
                                    <strong style="font-size: 11px; color: #2563eb; display: block; margin-bottom: 4px;">BUNDLING</strong>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_b_bk" value="BK"> BK (Basic)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_b_bc" value="BC"> BC (Custom)</label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; margin-bottom: 2px;"><input type="checkbox" id="feat_b_l" value="L"> L (Lengkap)</label>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Rincian Biaya Produksi & Laba (Kolom di Excel) -->
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="font-size: 12px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">
                                    2. Rincian Biaya Produksi, Cetak &amp; Laba Acara
                                </label>
                                <button type="button" onclick="autoDistributeCosts()" class="btn btn-secondary" style="font-size: 11px; padding: 3px 8px;">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Hitung Otomatis dari HPP
                                </button>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Crew Foto (Rp)</label>
                                    <input type="text" id="b_cost_crew_foto" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Cetak 4R (Rp)</label>
                                    <input type="text" id="b_cost_cetak_4r" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Packaging (Rp)</label>
                                    <input type="text" id="b_cost_packaging" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Album (Rp)</label>
                                    <input type="text" id="b_cost_album" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Cetak Jumbo &amp; Frame</label>
                                    <input type="text" id="b_cost_cetak_jumbo" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Crew Cinema (Rp)</label>
                                    <input type="text" id="b_cost_crew_cinema" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Laba Cinema (Rp)</label>
                                    <input type="text" id="b_laba_cinema" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Crew Liputan (Rp)</label>
                                    <input type="text" id="b_cost_crew_liputan" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Laba Liputan (Rp)</label>
                                    <input type="text" id="b_laba_liputan" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Crew Konten (Rp)</label>
                                    <input type="text" id="b_cost_crew_konten" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Laba Konten (Rp)</label>
                                    <input type="text" id="b_laba_konten" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Sales Fee / Fee Marketing</label>
                                    <input type="text" id="b_sales_fee" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Extras -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                3. Extras (Tambahan Biaya di Excel)
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1.5fr; gap: 10px; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Cetak Tambahan (Rp)</label>
                                    <input type="text" id="b_extra_cetak" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Penambahan Drive (Rp)</label>
                                    <input type="text" id="b_extra_drive" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Transportasi (Rp)</label>
                                    <input type="text" id="b_extra_transport" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="0" oninput="formatRupiahInput(this)">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px;">Keterangan Extras</label>
                                    <input type="text" id="b_extra_keterangan" class="form-control" style="font-size: 12px; padding: 6px 8px;" placeholder="Transport, FD tambahan, dll.">
                                </div>
                            </div>
                        </div>

                        <!-- 4. FG & VG Crew Fee Matrix -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                4. Pembagian Honor Crew (Masuk ke Tab BEBAN GAJI FG &amp; VG)
                            </label>
                            <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 8px;">Pilih crew yang bertugas dan masukkan nominal honor masing-masing:</p>
                                <div id="b_crew_fee_matrix_container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px; max-height: 180px; overflow-y: auto; padding-right: 4px;">
                                    <!-- Injected via JavaScript for 24 crew members -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background: rgba(80, 205, 137, 0.1); border: 1px solid rgba(80, 205, 137, 0.2); border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-size: 13px; color: var(--text-muted);" id="b_label_summary_income">Total Pendapatan (Paket + Add-On)</span>
                        <span style="font-size: 13px; font-weight: 600;" id="b_summary_income">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 13px; color: var(--text-muted);">Total Biaya Operasional</span>
                        <span style="font-size: 13px; font-weight: 600;" id="b_summary_cost">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid rgba(80, 205, 137, 0.2);">
                        <strong style="font-size: 15px; color: var(--text-dark);">Estimasi Profit Bersih</strong>
                        <strong style="font-size: 15px; color: var(--success);" id="b_summary_profit">Rp 0</strong>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Tanggal Bayar</label>
                    <input type="date" id="b_payment_date" class="form-control">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Status Pembayaran</label>
                        <select id="b_payment_status" class="form-control">
                            <option value="DP 1">DP 1</option>
                            <option value="DP 2">DP 2</option>
                            <option value="Termin 2">Termin 2</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Status Produksi</label>
                        <select id="b_production_status" class="form-control" onchange="toggleCustomStatus()">
                            <option>Pre-Event</option>
                            <option>Hari H</option>
                            <option>Proses Edit</option>
                            <option>Revisi</option>
                            <option>Selesai & Terkirim</option>
                            <option value="custom">+ Status custom...</option>
                        </select>
                        <div id="b_custom_status_container" style="display: none; gap: 8px; align-items: center;">
                            <input type="text" id="b_custom_status_input" class="form-control" placeholder="Tulis status baru, mis. Sele..." style="flex: 1;">
                            <button class="btn btn-secondary" onclick="cancelCustomStatus()" style="padding: 6px 12px; font-size: 12px; border-radius: 6px; border: 1px solid var(--border-color);">Batal</button>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Hasil Kerja</label>
                    <input type="text" id="b_result_link" class="form-control" placeholder="Link hasil kerja klien ini (https://...)">
                </div>
                
                <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="font-size: 14px; font-weight: 600;">Tim yang Handle Project</h4>
                        <button class="btn btn-secondary" onclick="addBookingTeam()" style="padding: 4px 12px; font-size: 12px;"><i class="fa-solid fa-plus"></i> Tambah Tim</button>
                    </div>
                    <div id="b_team_container">
                        <!-- Team list -->
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Catatan</label>
                    <textarea id="b_notes" class="form-control" placeholder="Catatan internal, permintaan khusus klien, dll." rows="3"></textarea>
                </div>
            </div>
            
            <div style="padding: 20px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px;">
                <button onclick="closeBookingModal()" class="btn btn-secondary">Batal</button>
                <button onclick="saveBooking()" class="btn btn-primary" id="btnSaveBooking">Simpan Booking</button>
            </div>
        </div>
    </div>
