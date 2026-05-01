{{-- ═══ MODAL: Tambah Mitra Baru ═══ --}}
@php
    $modalKlasifikasi = \App\Models\Klasifikasi::orderBy('nama', 'asc')->get();
@endphp

<div id="mitraModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; padding: 20px;"
    onclick="if(event.target===this) closeMitraModal()">

    {{-- Backdrop --}}
    <div id="mitraModalBackdrop"
        style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); transition: opacity 0.3s; opacity: 0;">
    </div>

    {{-- Modal Box --}}
    <div id="mitraModalBox"
        style="position: relative; width: 100%; max-width: 640px; max-height: 90vh; background: var(--surface); border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); overflow: hidden; transform: scale(0.9) translateY(20px); opacity: 0; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); display: flex; flex-direction: column;">

        {{-- Modal Header --}}
        <div style="display: flex; align-items: center; gap: 16px; padding: 24px 28px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(124,58,237,0.04)); flex-shrink: 0;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 8px 20px rgba(79,70,229,0.3);">
                <i class="fas fa-handshake"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.02em;">Tambah Mitra Baru</h3>
                <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-sub);">Lengkapi informasi instansi mitra kerjasama</p>
            </div>
            <button onclick="closeMitraModal()" type="button"
                style="width: 38px; height: 38px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface2); color: var(--text-sub); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0;"
                onmouseover="this.style.background='var(--surface)'; this.style.color='#ef4444'; this.style.borderColor='#fee2e2'"
                onmouseout="this.style.background='var(--surface2)'; this.style.color='var(--text-sub)'; this.style.borderColor='var(--border)'">
                <i class="fas fa-times" style="font-size: 14px;"></i>
            </button>
        </div>

        {{-- Modal Body (scrollable) --}}
        <div style="overflow-y: auto; flex: 1;" x-data="{
            kategori: '',
            negara: 'Indonesia',
            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: '',
            klasifikasiItems: [
                @foreach($modalKlasifikasi as $klas)
                    { id: '{{ $klas->id }}', label: '{{ addslashes($klas->nama) }}' },
                @endforeach
            ],
            get selectedKlasifikasi() {
                return this.klasifikasiItems.find(i => i.id === this.klasifikasiSelected);
            },
            get filteredKlasifikasi() {
                if (!this.klasifikasiSearch) return this.klasifikasiItems;
                const q = this.klasifikasiSearch.toLowerCase();
                return this.klasifikasiItems.filter(i => i.label.toLowerCase().includes(q));
            },
            countryOpen: false,
            countrySearch: '',
            countries: [
                'Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia','Austria','Azerbaijan',
                'Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia',
                'Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon',
                'Canada','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia',
                'Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','DR Congo','East Timor','Ecuador',
                'Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France',
                'Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau',
                'Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland',
                'Israel','Italy','Ivory Coast','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kosovo',
                'Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania',
                'Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius',
                'Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar','Namibia',
                'Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Korea','North Macedonia','Norway',
                'Oman','Pakistan','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland',
                'Portugal','Qatar','Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines',
                'Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore',
                'Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain','Sri Lanka','Sudan',
                'Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Togo','Tonga',
                'Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates',
                'United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe'
            ],
            get filteredCountries() {
                if (!this.countrySearch) return this.countries;
                const q = this.countrySearch.toLowerCase();
                return this.countries.filter(c => c.toLowerCase().includes(q));
            },
            submitting: false,
            errors: {},
            async submitMitra() {
                this.submitting = true;
                this.errors = {};

                const formData = new FormData(document.getElementById('mitraModalForm'));
                formData.set('id_klasifikasi', this.klasifikasiSelected);
                formData.set('kategori', this.kategori);
                formData.set('negara', this.kategori === 'internasional' ? this.negara : 'Indonesia');

                try {
                    const res = await fetch('{{ route('unit.mitra.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (res.status === 422) {
                        const data = await res.json();
                        this.errors = data.errors || {};
                        this.submitting = false;
                        return;
                    }

                    if (res.ok || res.status === 302) {
                        closeMitraModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Mitra baru berhasil ditambahkan.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    throw new Error('Unexpected response');
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menyimpan data.' });
                    this.submitting = false;
                }
            }
        }">
            <form id="mitraModalForm" @submit.prevent="submitMitra()">
                <div style="padding: 24px 28px;">

                    {{-- Row 1: Klasifikasi --}}
                    <div class="mc-group" style="margin-bottom: 18px;">
                        <label class="mc-label">Klasifikasi Mitra</label>
                        <div class="alpine-dropdown" @click.outside="klasifikasiOpen = false; klasifikasiSearch = ''">
                            <div class="ad-trigger no-icon" :class="{'active': klasifikasiOpen}"
                                @click="klasifikasiOpen = !klasifikasiOpen; $nextTick(() => { if(klasifikasiOpen) $refs.mkSearch.focus() })">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                    <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(79,70,229,0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span x-show="!selectedKlasifikasi" style="color: #9ca3af; font-size: 13px;">— Pilih Klasifikasi —</span>
                                    <span x-show="selectedKlasifikasi" x-text="selectedKlasifikasi ? selectedKlasifikasi.label : ''"
                                        style="font-weight: 600; font-size: 13px; color: var(--text);"></span>
                                </div>
                                <i class="fas fa-chevron-down" style="font-size: 11px; color: #9ca3af; transition: 0.3s; flex-shrink: 0;"
                                    :style="klasifikasiOpen ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                            <div class="ad-menu" x-show="klasifikasiOpen" x-transition style="max-height: 240px; display: flex; flex-direction: column; z-index: 10001;">
                                <div style="padding: 8px 12px; border-bottom: 1px solid var(--border); background: var(--surface); position: sticky; top: 0; z-index: 2;">
                                    <div style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                        <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                        <input x-ref="mkSearch" x-model="klasifikasiSearch" type="text" placeholder="Cari klasifikasi..."
                                            style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%; font-family: inherit;" @click.stop>
                                    </div>
                                </div>
                                <div style="overflow-y: auto; flex: 1;">
                                    <template x-for="item in filteredKlasifikasi" :key="item.id">
                                        <div class="ad-item" :class="{'selected': klasifikasiSelected === item.id}"
                                            @click="klasifikasiSelected = item.id; klasifikasiOpen = false; klasifikasiSearch = ''"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 16px; height: 16px; border-radius: 4px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;"
                                                :style="klasifikasiSelected === item.id ? 'background: var(--accent); border-color: var(--accent);' : ''">
                                                <i class="fas fa-check" style="font-size: 9px; color: #fff;" x-show="klasifikasiSelected === item.id"></i>
                                            </div>
                                            <span x-text="item.label" style="font-size: 13px;"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredKlasifikasi.length === 0" style="padding: 12px 16px; text-align: center; color: #9ca3af; font-size: 12px;">
                                        Tidak ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Nama & Kategori --}}
                    <div class="mc-grid-2" style="margin-bottom: 18px;">
                        <div class="mc-group">
                            <label class="mc-label">Nama Instansi / Mitra <span class="mc-req">*</span></label>
                            <div class="mc-input-wrap">
                                <input type="text" name="nama_mitra" required placeholder="Masukkan nama instansi/mitra" class="mc-input no-icon">
                            </div>
                            <template x-if="errors.nama_mitra">
                                <span style="color: #ef4444; font-size: 11px; margin-top: 4px; display: block;">
                                    <i class="fas fa-circle-exclamation"></i> <span x-text="errors.nama_mitra[0]"></span>
                                </span>
                            </template>
                        </div>
                        <div class="mc-group" x-data="{ katOpen: false }">
                            <label class="mc-label">Kategori <span class="mc-req">*</span></label>
                            <div class="alpine-dropdown" @click.outside="katOpen = false">
                                <div class="ad-trigger no-icon" :class="{'active': katOpen}" @click="katOpen = !katOpen">
                                    <span x-text="kategori === 'nasional' ? 'Nasional' : (kategori === 'internasional' ? 'Internasional' : '— Pilih Kategori —')"
                                        style="font-size: 13px;"></span>
                                    <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s"
                                        :style="katOpen ? 'transform: rotate(180deg)' : ''"></i>
                                </div>
                                <div class="ad-menu" x-show="katOpen" x-transition style="z-index: 10001;">
                                    <div class="ad-item" :class="{'selected': kategori === 'nasional'}"
                                        @click="kategori = 'nasional'; negara = 'Indonesia'; katOpen = false">Nasional</div>
                                    <div class="ad-item" :class="{'selected': kategori === 'internasional'}"
                                        @click="kategori = 'internasional'; negara = ''; katOpen = false">Internasional</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Negara (International only) --}}
                    <div x-show="kategori === 'internasional'" x-transition style="margin-bottom: 18px;">
                        <div class="mc-group">
                            <label class="mc-label"><i class="fas fa-globe-americas" style="color: var(--accent); margin-right: 6px;"></i>Negara</label>
                            <div class="alpine-dropdown" @click.outside="countryOpen = false; countrySearch = ''">
                                <div class="ad-trigger" :class="{'active': countryOpen}"
                                    @click="countryOpen = !countryOpen; $nextTick(() => { if(countryOpen) $refs.mkCountrySearch.focus() })">
                                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                        <i class="fas fa-flag" style="color: #9ca3af; font-size: 13px; flex-shrink: 0;"></i>
                                        <span x-show="!negara" style="color: #9ca3af; font-size: 13px;">— Pilih Negara —</span>
                                        <span x-show="negara" x-text="negara" style="font-weight: 500; font-size: 13px;"></span>
                                    </div>
                                    <i class="fas fa-chevron-down" style="font-size: 10px; transition: 0.3s; flex-shrink: 0;"
                                        :style="countryOpen ? 'transform: rotate(180deg)' : ''"></i>
                                </div>
                                <div class="ad-menu" x-show="countryOpen" x-transition style="max-height: 240px; overflow: hidden; display: flex; flex-direction: column; z-index: 10001;">
                                    <div style="padding: 8px 12px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); z-index: 2;">
                                        <div style="display: flex; align-items: center; gap: 8px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px;">
                                            <i class="fas fa-search" style="font-size: 12px; color: #9ca3af;"></i>
                                            <input x-ref="mkCountrySearch" x-model="countrySearch" type="text" placeholder="Cari negara..."
                                                style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--text); width: 100%; font-family: inherit;" @click.stop>
                                        </div>
                                    </div>
                                    <div style="overflow-y: auto; max-height: 180px; flex: 1;">
                                        <template x-for="country in filteredCountries" :key="country">
                                            <div class="ad-item" :class="{'selected': negara === country}"
                                                @click="negara = country; countryOpen = false; countrySearch = ''"
                                                x-text="country"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 4: Alamat --}}
                    <div class="mc-group" style="margin-bottom: 18px;">
                        <label class="mc-label">Alamat</label>
                        <div class="mc-input-wrap">
                            <i class="fas fa-map-marker-alt mc-icon-left" style="top: 14px;"></i>
                            <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap mitra..."
                                class="mc-input" style="resize: vertical; min-height: 60px;"></textarea>
                        </div>
                    </div>

                    {{-- Row 5: Telepon & Website --}}
                    <div class="mc-grid-2">
                        <div class="mc-group">
                            <label class="mc-label">Nomor Telepon</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-phone mc-icon-left"></i>
                                <input type="text" name="telp" placeholder="Contoh: 021-12345678" class="mc-input">
                            </div>
                        </div>
                        <div class="mc-group">
                            <label class="mc-label">Website</label>
                            <div class="mc-input-wrap">
                                <i class="fas fa-globe mc-icon-left"></i>
                                <input type="text" name="website" placeholder="https://www.example.com" class="mc-input">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div style="display: flex; justify-content: flex-end; gap: 10px; padding: 18px 28px; border-top: 1px solid var(--border); background: var(--surface2); flex-shrink: 0;">
                    <button type="button" onclick="closeMitraModal()"
                        style="background: var(--surface); color: var(--text); border: 1px solid var(--border); padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 7px;"
                        onmouseover="this.style.background='var(--surface2)'"
                        onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" :disabled="submitting"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; border: none; padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 14px rgba(79,70,229,0.35); transition: all 0.3s;"
                        :style="submitting ? 'opacity: 0.6; cursor: not-allowed;' : ''"
                        onmouseover="if(!this.disabled) { this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(79,70,229,0.45)'; }"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(79,70,229,0.35)';">
                        <template x-if="!submitting">
                            <span style="display: inline-flex; align-items: center; gap: 7px;"><i class="fas fa-save"></i> Simpan Mitra</span>
                        </template>
                        <template x-if="submitting">
                            <span style="display: inline-flex; align-items: center; gap: 7px;"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openMitraModal() {
    const modal = document.getElementById('mitraModal');
    const backdrop = document.getElementById('mitraModalBackdrop');
    const box = document.getElementById('mitraModalBox');

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(() => {
        backdrop.style.opacity = '1';
        box.style.transform = 'scale(1) translateY(0)';
        box.style.opacity = '1';
    });
}

function closeMitraModal() {
    const modal = document.getElementById('mitraModal');
    const backdrop = document.getElementById('mitraModalBackdrop');
    const box = document.getElementById('mitraModalBox');

    backdrop.style.opacity = '0';
    box.style.transform = 'scale(0.9) translateY(20px)';
    box.style.opacity = '0';

    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        // Reset form
        const form = document.getElementById('mitraModalForm');
        if (form) form.reset();
    }, 300);
}

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('mitraModal').style.display === 'flex') {
        closeMitraModal();
    }
});
</script>
