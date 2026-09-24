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

    const defaultProvinces = [
        { id: '11', name: 'Aceh' },
        { id: '12', name: 'Sumatera Utara' },
        { id: '13', name: 'Sumatera Barat' },
        { id: '14', name: 'Riau' },
        { id: '15', name: 'Jambi' },
        { id: '16', name: 'Sumatera Selatan' },
        { id: '17', name: 'Bengkulu' },
        { id: '18', name: 'Lampung' },
        { id: '19', name: 'Kepulauan Bangka Belitung' },
        { id: '21', name: 'Kepulauan Riau' },
        { id: '31', name: 'DKI Jakarta' },
        { id: '32', name: 'Jawa Barat' },
        { id: '33', name: 'Jawa Tengah' },
        { id: '34', name: 'DI Yogyakarta' },
        { id: '35', name: 'Jawa Timur' },
        { id: '36', name: 'Banten' },
        { id: '51', name: 'Bali' },
        { id: '52', name: 'Nusa Tenggara Barat' },
        { id: '53', name: 'Nusa Tenggara Timur' },
        { id: '61', name: 'Kalimantan Barat' },
        { id: '62', name: 'Kalimantan Tengah' },
        { id: '63', name: 'Kalimantan Selatan' },
        { id: '64', name: 'Kalimantan Timur' },
        { id: '65', name: 'Kalimantan Utara' },
        { id: '71', name: 'Sulawesi Utara' },
        { id: '72', name: 'Sulawesi Tengah' },
        { id: '73', name: 'Sulawesi Selatan' },
        { id: '74', name: 'Sulawesi Tenggara' },
        { id: '75', name: 'Gorontalo' },
        { id: '76', name: 'Sulawesi Barat' },
        { id: '81', name: 'Maluku' },
        { id: '82', name: 'Maluku Utara' },
        { id: '91', name: 'Papua Barat' },
        { id: '92', name: 'Papua Barat Daya' },
        { id: '93', name: 'Papua Selatan' },
        { id: '94', name: 'Papua' },
        { id: '95', name: 'Papua Tengah' },
        { id: '96', name: 'Papua Pegunungan' }
    ];

    function toTitleCase(str) {
        if (!str) return '';
        return str.toLowerCase()
            .replace(/(?:^|\s|\/|-|\()\S/g, function (match) {
                return match.toUpperCase();
            })
            .replace(/\b(Dki|Di|Upa)\b/g, function (match) {
                return match.toUpperCase();
            })
            .replace(/\bIii\b/g, 'III')
            .replace(/\bIi\b/g, 'II')
            .replace(/\bIv\b/g, 'IV')
            .replace(/\bVi\b/g, 'VI')
            .replace(/\bV\b/g, 'V');
    }

    const WilayahService = {
        cache: {
            provinces: null,
            regencies: {},
            districts: {},
            villages: {}
        },
        async getProvinces() {
            if (this.cache.provinces) return this.cache.provinces;
            try {
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                if (res.ok) {
                    const data = await res.json();
                    this.cache.provinces = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.provinces;
                }
            } catch (e) {
                console.error('Failed to fetch provinces from API, using default list:', e);
            }
            this.cache.provinces = defaultProvinces;
            return this.cache.provinces;
        },
        async getRegencies(provinceId) {
            if (!provinceId) return [];
            if (this.cache.regencies[provinceId]) return this.cache.regencies[provinceId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.regencies[provinceId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.regencies[provinceId];
                }
            } catch (e) {
                console.error('Failed to fetch regencies:', e);
            }
            return [];
        },
        async getDistricts(regencyId) {
            if (!regencyId) return [];
            if (this.cache.districts[regencyId]) return this.cache.districts[regencyId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.districts[regencyId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.districts[regencyId];
                }
            } catch (e) {
                console.error('Failed to fetch districts:', e);
            }
            return [];
        },
        async getVillages(districtId) {
            if (!districtId) return [];
            if (this.cache.villages[districtId]) return this.cache.villages[districtId];
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`);
                if (res.ok) {
                    const data = await res.json();
                    this.cache.villages[districtId] = data.map(item => ({
                        id: String(item.id),
                        name: toTitleCase(item.name)
                    }));
                    return this.cache.villages[districtId];
                }
            } catch (e) {
                console.error('Failed to fetch villages:', e);
            }
            return [];
        }
    };

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
            cityId: '',
            kotaInternasional: '',
            kecamatan: '',
            districtId: '',
            kelurahan: '',
            villageId: '',
            telp: '',
            website: '',
            kategori: '',
            negara: 'Indonesia',
            provinsi: '',
            provinceId: '',
            provincesList: [...defaultProvinces],
            cityList: [],
            districtList: [],
            villageList: [],
            loadingCities: false,
            loadingDistricts: false,
            loadingVillages: false,
            katOpen: false,
            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: '',
            klasifikasiItems: config.klasifikasiItems || [],
            countryOpen: false,
            countrySearch: '',
            countries: countries,
            provinceOpen: false,
            provinceSearch: '',
            cityOpen: false,
            citySearch: '',
            districtOpen: false,
            districtSearch: '',
            villageOpen: false,
            villageSearch: '',
            submitting: false,
            errors: {},
            async init() {
                try {
                    const provs = await WilayahService.getProvinces();
                    if (provs && provs.length > 0) {
                        this.provincesList = provs;
                    }
                } catch (e) {}
            },
            async selectProvince(item) {
                if (!item) {
                    this.provinsi = '';
                    this.provinceId = '';
                    this.selectCity(null);
                    this.cityList = [];
                } else {
                    this.provinsi = item.name;
                    this.provinceId = item.id;
                    this.selectCity(null);
                    this.cityList = [];
                    this.loadingCities = true;
                    this.cityList = await WilayahService.getRegencies(item.id);
                    this.loadingCities = false;
                }
                this.provinceOpen = false;
                this.provinceSearch = '';
            },
            async selectCity(item) {
                if (!item) {
                    this.kota = '';
                    this.cityId = '';
                    this.selectDistrict(null);
                    this.districtList = [];
                } else {
                    this.kota = item.name;
                    this.cityId = item.id;
                    this.selectDistrict(null);
                    this.districtList = [];
                    this.loadingDistricts = true;
                    this.districtList = await WilayahService.getDistricts(item.id);
                    this.loadingDistricts = false;
                }
                this.cityOpen = false;
                this.citySearch = '';
            },
            async selectDistrict(item) {
                if (!item) {
                    this.kecamatan = '';
                    this.districtId = '';
                    this.selectVillage(null);
                    this.villageList = [];
                } else {
                    this.kecamatan = item.name;
                    this.districtId = item.id;
                    this.selectVillage(null);
                    this.villageList = [];
                    this.loadingVillages = true;
                    this.villageList = await WilayahService.getVillages(item.id);
                    this.loadingVillages = false;
                }
                this.districtOpen = false;
                this.districtSearch = '';
            },
            selectVillage(item) {
                if (!item) {
                    this.kelurahan = '';
                    this.villageId = '';
                } else {
                    this.kelurahan = item.name;
                    this.villageId = item.id;
                }
                this.villageOpen = false;
                this.villageSearch = '';
            },
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
                    return this.provincesList;
                }

                const query = this.provinceSearch.toLowerCase();
                return this.provincesList.filter((p) => p.name.toLowerCase().includes(query));
            },
            get filteredCities() {
                if (!this.citySearch) {
                    return this.cityList;
                }

                const query = this.citySearch.toLowerCase();
                return this.cityList.filter((c) => c.name.toLowerCase().includes(query));
            },
            get filteredDistricts() {
                if (!this.districtSearch) {
                    return this.districtList;
                }

                const query = this.districtSearch.toLowerCase();
                return this.districtList.filter((d) => d.name.toLowerCase().includes(query));
            },
            get filteredVillages() {
                if (!this.villageSearch) {
                    return this.villageList;
                }

                const query = this.villageSearch.toLowerCase();
                return this.villageList.filter((v) => v.name.toLowerCase().includes(query));
            },
            async setEditData(detail) {
                this.mitraId = detail.id || '';
                this.nama_mitra = detail.nama_mitra || '';
                this.klasifikasiSelected = String(detail.id_klasifikasi || '');
                this.kategori = detail.kategori || '';
                this.negara = detail.negara || 'Indonesia';
                this.provinsi = detail.provinsi || '';
                this.provinceId = '';
                this.kota = detail.kategori === 'nasional' ? (detail.kota || '') : '';
                this.cityId = '';
                this.kotaInternasional = detail.kategori === 'internasional' ? (detail.kota || '') : '';
                this.kecamatan = detail.kecamatan || '';
                this.districtId = '';
                this.kelurahan = detail.kelurahan || '';
                this.villageId = '';
                this.alamat = detail.alamat || '';
                this.telp = detail.telp || '';
                this.website = detail.website || '';
                this.cityList = [];
                this.districtList = [];
                this.villageList = [];
                this.klasifikasiOpen = false;
                this.klasifikasiSearch = '';
                this.katOpen = false;
                this.countryOpen = false;
                this.countrySearch = '';
                this.provinceOpen = false;
                this.provinceSearch = '';
                this.cityOpen = false;
                this.citySearch = '';
                this.districtOpen = false;
                this.districtSearch = '';
                this.villageOpen = false;
                this.villageSearch = '';
                this.submitting = false;
                this.errors = {};

                if (this.kategori === 'nasional' && this.provinsi) {
                    const normProv = this.provinsi.trim().toLowerCase();
                    let foundProv = this.provincesList.find(p => p.name.toLowerCase() === normProv || normProv.includes(p.name.toLowerCase()) || p.name.toLowerCase().includes(normProv));
                    if (!foundProv) {
                        const provs = await WilayahService.getProvinces();
                        if (provs && provs.length > 0) {
                            this.provincesList = provs;
                            foundProv = this.provincesList.find(p => p.name.toLowerCase() === normProv || normProv.includes(p.name.toLowerCase()) || p.name.toLowerCase().includes(normProv));
                        }
                    }

                    if (foundProv) {
                        this.provinceId = foundProv.id;
                        this.loadingCities = true;
                        this.cityList = await WilayahService.getRegencies(foundProv.id);
                        this.loadingCities = false;

                        if (this.kota) {
                            const normKota = this.kota.trim().toLowerCase();
                            const foundCity = this.cityList.find(c => c.name.toLowerCase() === normKota || normKota.includes(c.name.toLowerCase()) || c.name.toLowerCase().includes(normKota));
                            if (foundCity) {
                                this.cityId = foundCity.id;
                                this.loadingDistricts = true;
                                this.districtList = await WilayahService.getDistricts(foundCity.id);
                                this.loadingDistricts = false;

                                if (this.kecamatan) {
                                    const normKec = this.kecamatan.trim().toLowerCase();
                                    const foundDist = this.districtList.find(d => d.name.toLowerCase() === normKec || normKec.includes(d.name.toLowerCase()) || d.name.toLowerCase().includes(normKec));
                                    if (foundDist) {
                                        this.districtId = foundDist.id;
                                        this.loadingVillages = true;
                                        this.villageList = await WilayahService.getVillages(foundDist.id);
                                        this.loadingVillages = false;

                                        if (this.kelurahan) {
                                            const normKel = this.kelurahan.trim().toLowerCase();
                                            const foundVill = this.villageList.find(v => v.name.toLowerCase() === normKel || normKel.includes(v.name.toLowerCase()) || v.name.toLowerCase().includes(normKel));
                                            if (foundVill) {
                                                this.villageId = foundVill.id;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
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
                formData.set('kota', this.kategori === 'nasional' ? this.kota : (this.kotaInternasional || ''));
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
