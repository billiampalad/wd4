(function () {
    const countries = [
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia',
        'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon',
        'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica', 'Croatia',
        'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'DR Congo', 'East Timor', 'Ecuador',
        'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau',
        'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland',
        'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo',
        'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
        'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius',
        'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia',
        'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway',
        'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland',
        'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines',
        'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore',
        'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
        'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Togo', 'Tonga',
        'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
        'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
    ];

    const provinces = [
        'Aceh', 'Bali', 'Banten', 'Bengkulu', 'DI Yogyakarta', 'DKI Jakarta',
        'Gorontalo', 'Jambi', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur',
        'Kalimantan Barat', 'Kalimantan Selatan', 'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara',
        'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara',
        'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Papua Barat Daya',
        'Papua Pegunungan', 'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat',
        'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara',
        'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara'
    ];

    function getEditModalParts() {
        return {
            modal: document.getElementById('mitraEditModal'),
            backdrop: document.getElementById('mitraEditModalBackdrop'),
            box: document.getElementById('mitraEditModalBox')
        };
    }

    window.openMitraEditModal = function (id, namaMitra, idKlasifikasi, kategori, negara, alamat, telp, website, provinsi, kota, kecamatan, kelurahan) {
        const parts = getEditModalParts();

        if (!parts.modal || !parts.backdrop || !parts.box) {
            return;
        }

        window.dispatchEvent(new CustomEvent('set-mitra-edit-data', {
            detail: {
                id: id,
                nama_mitra: namaMitra,
                id_klasifikasi: idKlasifikasi,
                kategori: kategori,
                negara: negara,
                provinsi: provinsi || '',
                kota: kota || '',
                kecamatan: kecamatan || '',
                kelurahan: kelurahan || '',
                alamat: alamat,
                telp: telp,
                website: website
            }
        }));

        parts.modal.removeAttribute('hidden');
        parts.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            parts.backdrop.style.opacity = '1';
            parts.box.style.transform = 'scale(1) translateY(0)';
            parts.box.style.opacity = '1';
        });
    };

    window.closeMitraEditModal = function () {
        const parts = getEditModalParts();

        if (!parts.modal || !parts.backdrop || !parts.box) {
            return;
        }

        parts.backdrop.style.opacity = '0';
        parts.box.style.transform = 'scale(0.9) translateY(20px)';
        parts.box.style.opacity = '0';

        setTimeout(function () {
            parts.modal.style.display = 'none';
            parts.modal.setAttribute('hidden', '');
            document.body.style.overflow = '';
        }, 300);
    };

    window.createMitraEditModal = function (config) {
        return {
            mitraId: '',
            nama_mitra: '',
            alamat: '',
            kota: '',
            kecamatan: '',
            kelurahan: '',
            telp: '',
            website: '',
            kategori: '',
            negara: 'Indonesia',
            provinsi: '',
            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: '',
            klasifikasiItems: config.klasifikasiItems || [],
            countryOpen: false,
            countrySearch: '',
            countries: countries,
            provinceOpen: false,
            provinceSearch: '',
            provinces: provinces,
            submitting: false,
            errors: {},
            get selectedKlasifikasi() {
                return this.klasifikasiItems.find((item) => item.id === this.klasifikasiSelected);
            },
            get filteredKlasifikasi() {
                if (!this.klasifikasiSearch) {
                    return this.klasifikasiItems;
                }

                const query = this.klasifikasiSearch.toLowerCase();
                return this.klasifikasiItems.filter((item) => item.label.toLowerCase().includes(query));
            },
            get filteredCountries() {
                if (!this.countrySearch) {
                    return this.countries;
                }

                const query = this.countrySearch.toLowerCase();
                return this.countries.filter((country) => country.toLowerCase().includes(query));
            },
            get filteredProvinces() {
                if (!this.provinceSearch) {
                    return this.provinces;
                }

                const query = this.provinceSearch.toLowerCase();
                return this.provinces.filter((p) => p.toLowerCase().includes(query));
            },
            setEditData(detail) {
                this.mitraId = detail.id || '';
                this.nama_mitra = detail.nama_mitra || '';
                this.klasifikasiSelected = String(detail.id_klasifikasi || '');
                this.kategori = detail.kategori || '';
                this.negara = detail.negara || 'Indonesia';
                this.provinsi = detail.provinsi || '';
                this.kota = detail.kota || '';
                this.kecamatan = detail.kecamatan || '';
                this.kelurahan = detail.kelurahan || '';
                this.alamat = detail.alamat || '';
                this.telp = detail.telp || '';
                this.website = detail.website || '';
                this.klasifikasiOpen = false;
                this.klasifikasiSearch = '';
                this.countryOpen = false;
                this.countrySearch = '';
                this.provinceOpen = false;
                this.provinceSearch = '';
                this.submitting = false;
                this.errors = {};
            },
            async submitMitra() {
                this.submitting = true;
                this.errors = {};

                const formData = new FormData();
                formData.set('_token', document.querySelector('meta[name=csrf-token]').content);
                formData.set('_method', 'PUT');
                formData.set('id_klasifikasi', this.klasifikasiSelected);
                formData.set('nama_mitra', this.nama_mitra);
                formData.set('kategori', this.kategori);
                formData.set('negara', this.kategori === 'internasional' ? this.negara : 'Indonesia');
                formData.set('provinsi', this.kategori === 'nasional' ? this.provinsi : '');
                formData.set('kota', this.kota);
                formData.set('kecamatan', this.kategori === 'nasional' ? this.kecamatan : '');
                formData.set('kelurahan', this.kategori === 'nasional' ? this.kelurahan : '');
                formData.set('alamat', this.alamat);
                formData.set('telp', this.telp);
                formData.set('website', this.website);

                try {
                    const response = await fetch(`${config.baseUrl}/${this.mitraId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                        if (window.CustomAlert) {
                            const firstError = Object.values(this.errors)[0]?.[0] || 'Validasi gagal.';
                            CustomAlert.error(firstError);
                        }
                        return;
                    }

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            closeMitraEditModal();
                            document.dispatchEvent(new CustomEvent('table:updated'));
                            if (window.CustomAlert) {
                                CustomAlert.success(result.message || 'Data mitra berhasil diperbarui.');
                            }

                            if (typeof window.refreshMitraIndex === 'function') {
                                window.refreshMitraIndex();
                            }
                            return;
                        }
                    }

                    throw new Error('Unexpected response');
                } catch (error) {
                    if (window.CustomAlert) {
                        CustomAlert.error('Terjadi kesalahan saat menyimpan data.');
                    } else {
                        alert('Terjadi kesalahan saat menyimpan data.');
                    }
                } finally {
                    this.submitting = false;
                }
            }
        };
    };

    window.createMitraEditModalFromElement = function (element) {
        let klasifikasiItems = [];

        try {
            klasifikasiItems = JSON.parse(element.dataset.klasifikasiItems || '[]');
        } catch (error) {
            console.error('Gagal membaca data klasifikasi modal edit mitra.', error);
            klasifikasiItems = [];
        }

        return window.createMitraEditModal({
            baseUrl: element.dataset.baseUrl,
            klasifikasiItems: klasifikasiItems
        });
    };

    document.addEventListener('keydown', function (event) {
        const modal = document.getElementById('mitraEditModal');

        if (event.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeMitraEditModal();
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'mitraEditModal') {
            closeMitraEditModal();
            return;
        }

        if (event.target.closest('[data-mitra-edit-modal-close]')) {
            closeMitraEditModal();
        }
    });
})();
