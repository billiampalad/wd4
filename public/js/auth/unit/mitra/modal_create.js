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

    function getCreateModalParts() {
        return {
            modal: document.getElementById('mitraModal'),
            backdrop: document.getElementById('mitraModalBackdrop'),
            box: document.getElementById('mitraModalBox')
        };
    }

    window.openMitraModal = function () {
        const parts = getCreateModalParts();

        if (!parts.modal || !parts.backdrop || !parts.box) {
            return;
        }

        parts.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            parts.backdrop.style.opacity = '1';
            parts.box.style.transform = 'scale(1) translateY(0)';
            parts.box.style.opacity = '1';
        });
    };

    window.closeMitraModal = function () {
        const parts = getCreateModalParts();

        if (!parts.modal || !parts.backdrop || !parts.box) {
            return;
        }

        parts.backdrop.style.opacity = '0';
        parts.box.style.transform = 'scale(0.9) translateY(20px)';
        parts.box.style.opacity = '0';

        setTimeout(function () {
            parts.modal.style.display = 'none';
            document.body.style.overflow = '';

            const form = document.getElementById('mitraModalForm');
            if (form) {
                form.reset();
            }
        }, 300);
    };

    window.createMitraModal = function (config) {
        return {
            kategori: '',
            negara: 'Indonesia',
            klasifikasiOpen: false,
            klasifikasiSearch: '',
            klasifikasiSelected: '',
            klasifikasiItems: config.klasifikasiItems || [],
            countryOpen: false,
            countrySearch: '',
            countries: countries,
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
            async submitMitra() {
                this.submitting = true;
                this.errors = {};

                const form = document.getElementById('mitraModalForm');
                const formData = new FormData(form);

                formData.set('id_klasifikasi', this.klasifikasiSelected);
                formData.set('kategori', this.kategori);
                formData.set('negara', this.kategori === 'internasional' ? this.negara : 'Indonesia');

                try {
                    const response = await fetch(config.storeUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                        this.submitting = false;
                        return;
                    }

                    if (response.ok) {
                        const result = await response.json();
                        closeMitraModal();

                        window.dispatchEvent(new CustomEvent('mitra-added', {
                            detail: result.data
                        }));

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Mitra baru berhasil ditambahkan.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        return;
                    }

                    if (response.status === 302) {
                        window.location.reload();
                        return;
                    }

                    throw new Error('Unexpected response');
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan data.'
                    });
                    this.submitting = false;
                }
            }
        };
    };

    window.createMitraModalFromElement = function (element) {
        let klasifikasiItems = [];

        try {
            klasifikasiItems = JSON.parse(element.dataset.klasifikasiItems || '[]');
        } catch (error) {
            console.error('Gagal membaca data klasifikasi modal mitra.', error);
            klasifikasiItems = [];
        }

        return window.createMitraModal({
            storeUrl: element.dataset.storeUrl,
            klasifikasiItems: klasifikasiItems
        });
    };

    document.addEventListener('keydown', function (event) {
        const modal = document.getElementById('mitraModal');

        if (event.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeMitraModal();
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'mitraModal') {
            closeMitraModal();
            return;
        }

        if (event.target.closest('[data-mitra-modal-close]')) {
            closeMitraModal();
        }
    });
})();
