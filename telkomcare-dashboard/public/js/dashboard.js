document.addEventListener('DOMContentLoaded', function() {
    try {
        const elements = {
            userMenuButton: document.getElementById('userMenuButton'),
            userMenuDropdown: document.getElementById('userMenuDropdown'),
            startDateInput: document.getElementById('startDate'),
            endDateInput: document.getElementById('endDate'),
            regionFilter: document.getElementById('regionFilter'),
            cityFilter: document.getElementById('cityFilter'),
            categoryFilter: document.getElementById('categoryFilter'),
            applyFiltersBtn: document.getElementById('applyFilters'),
            clearFiltersBtn: document.getElementById('clearFilters'),
            dashboardTableBody: document.getElementById('dashboardTableBody'),
            addDataBtn: document.getElementById('addDataBtn'),
            addDataModal: document.getElementById('addDataModal'),
            closeModalBtn: document.getElementById('closeModalBtn'),
            addDataForm: document.getElementById('addDataForm'),
            modalRegion: document.getElementById('modalRegion'),
            modalCity: document.getElementById('modalCity'),
            modalErrors: document.getElementById('modal-errors')
        };

        // --- Validasi Elemen DOM Kritis ---
        for (const key in elements) {
            if (!elements[key]) {
                console.error(`Critical Error: Element with ID '${key}' was not found in the DOM.`);
                // alert(`Critical Error: Missing element with ID '${key}'. Please check the HTML.`);
                return;
            }
        }

        let allRegionsData = []; // Akan diisi dari /api/regions-cities
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseUrl = window.location.origin;

        // --- Fungsi Utilitas ---
        // Fungsi untuk mengambil data dari API (asli untuk regions-cities)
        async function fetchData(url) {
            const fullUrl = `${baseUrl}${url}`;
            const response = await fetch(fullUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        }

        // Fungsi untuk mengirim data ke API (POST)
        async function postData(url, data) {
            const fullUrl = `${baseUrl}${url}`;
            const response = await fetch(fullUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        }

        function formatNumber(num) {
            return (num || 0).toLocaleString('id-ID');
        }

        function formatPercentage(num) {
            if (num === null || typeof num === 'undefined') {
                return '0,00%';
            }
            const numericNum = parseFloat(num);
            if (isNaN(numericNum)) {
                return '0,00%';
            }
            return `${numericNum.toFixed(2).replace('.', ',')}%`;
        }

        function createTableCell(content, classes = '') {
            const cell = document.createElement('td');
            cell.className = `py-2 px-2 text-center ${classes}`;
            cell.innerHTML = content;
            return cell;
        }

        // Fungsi untuk menghasilkan data dummy K1, K2, K3
        function generateDummyKData() {
            const sid = Math.floor(Math.random() * 500) + 50;
            const comply = Math.floor(sid * (0.6 + Math.random() * 0.3)); // 60-90% comply
            const not_comply = sid - comply;
            const total = sid;
            const target = parseFloat((70 + Math.random() * 20).toFixed(2)); // 70-90% target
            const achievement = parseFloat(((comply / sid) * 100).toFixed(2));
            const ttr_comply = Math.floor(Math.random() * 100) + 10;

            return { sid, comply, not_comply, total, target, ttr_comply, achievement };
        }

        // --- Fungsi Utama untuk Membuat Baris Tabel ---
        function createRow(data, level = 'region') { // Tambah parameter level
            const row = document.createElement('tr');
            const totalTickets = [data.k1?.total, data.k2?.total, data.k3?.total].map(count => parseInt(count || 0, 10)).reduce((sum, count) => sum + count, 0);

            const achievementValues = [data.k1?.achievement, data.k2?.achievement, data.k3?.achievement]
                .filter(v => typeof v === 'number' && v !== null);

            const avgAchievement = achievementValues.length > 0 ?
                achievementValues.reduce((a, b) => a + b, 0) / achievementValues.length : 0;

            let nameContent = '';
            let rowClasses = '';
            let dataAttributes = [];
            let hasChildren = false;

            switch (level) {
                case 'region':
                    rowClasses = 'bg-gray-700 font-bold cursor-pointer hover:bg-gray-600 region-row';
                    dataAttributes.push(`data-region-id="${data.id}"`);
                    hasChildren = data.cities_detail && data.cities_detail.length > 0;
                    nameContent = `<i class="fas fa-chevron-right fa-fw mr-2 transition-transform duration-200"></i> ${data.name}`;
                    break;
                case 'city':
                    rowClasses = 'bg-gray-800 cursor-pointer hover:bg-gray-700 city-row hidden';
                    dataAttributes.push(`data-city-id="${data.id}"`);
                    dataAttributes.push(`data-parent-id="${data.region_id}"`);
                    hasChildren = data.hsas_detail && data.hsas_detail.length > 0;
                    nameContent = `<span class="pl-6">${data.name}</span>`;
                    if (hasChildren) {
                        nameContent = `<span class="pl-6"><i class="fas fa-chevron-right fa-fw mr-2 transition-transform duration-200"></i> ${data.name}</span>`;
                    }
                    break;
                case 'hsa':
                    rowClasses = 'bg-gray-800 cursor-pointer hover:bg-gray-700 hsa-row hidden';
                    dataAttributes.push(`data-hsa-id="${data.id}"`);
                    dataAttributes.push(`data-parent-id="${data.city_id}"`);
                    hasChildren = data.stos_detail && data.stos_detail.length > 0;
                    nameContent = `<span class="pl-12">${data.name}</span>`;
                    if (hasChildren) {
                        nameContent = `<span class="pl-12"><i class="fas fa-chevron-right fa-fw mr-2 transition-transform duration-200"></i> ${data.name}</span>`;
                    }
                    break;
                case 'sto':
                    rowClasses = 'bg-gray-800 sto-row hidden';
                    dataAttributes.push(`data-sto-id="${data.id}"`);
                    dataAttributes.push(`data-parent-id="${data.hsa_id}"`);
                    nameContent = `<span class="pl-18">${data.name}</span>`;
                    // STO does not have children to expand, so hasChildren remains false
                    break;
            }

            row.className = rowClasses;
            row.setAttribute('data-level', level);
            dataAttributes.forEach(attr => {
                const [key, value] = attr.split('=');
                row.setAttribute(key, value.replace(/"/g, ''));
            });

            row.setAttribute('data-expandable', hasChildren ? 'true' : 'false');


            const nameCell = document.createElement('td');
            nameCell.className = 'py-2 px-2 text-left';
            nameCell.innerHTML = nameContent;
            row.appendChild(nameCell);

            ['k1', 'k2', 'k3'].forEach(k => {
                const cat = data[k] || {};
                const borderClass = k !== 'k3' ? 'border-r border-gray-600' : '';
                row.appendChild(createTableCell(formatNumber(cat.sid), 'border-l border-gray-600'));
                row.appendChild(createTableCell(formatNumber(cat.comply)));
                row.appendChild(createTableCell(formatNumber(cat.not_comply)));
                row.appendChild(createTableCell(formatNumber(cat.total)));
                row.appendChild(createTableCell(formatPercentage(cat.target)));
                row.appendChild(createTableCell(formatNumber(cat.ttr_comply)));
                row.appendChild(createTableCell(formatPercentage(cat.achievement), `${borderClass} font-semibold`));
            });
            row.appendChild(createTableCell(formatPercentage(avgAchievement), 'font-bold text-white border-l border-r border-gray-600'));
            row.appendChild(createTableCell(formatNumber(totalTickets), 'font-bold text-white'));
            return row;
        }

        // --- Fungsi Utama untuk Memuat Data Dashboard ---
        async function loadDashboardData() {
            elements.dashboardTableBody.innerHTML = '<tr><td colspan="25" class="text-center py-4">Loading data...</td></tr>';
            const params = new URLSearchParams({
                ...(elements.startDateInput.value && { start_date: elements.startDateInput.value }),
                ...(elements.endDateInput.value && { end_date: elements.endDateInput.value }),
                ...(elements.regionFilter.value && { region_id: elements.regionFilter.value }),
                ...(elements.cityFilter.value && { city_id: elements.cityFilter.value }),
                ...(elements.categoryFilter.value && { category: elements.categoryFilter.value }),
            }).toString();

            try {
                // Mengambil data dari API yang sudah ada
                const originalData = await fetchData(`/api/dashboard-data?${params}`);

                // --- MODIFIKASI: Tambahkan data dummy HSA dan STO ---
                const processedData = originalData.map(region => {
                    const newRegion = { ...region };
                    if (newRegion.cities_detail && newRegion.cities_detail.length > 0) {
                        newRegion.cities_detail = newRegion.cities_detail.map(city => {
                            const newCity = { ...city, region_id: newRegion.id }; // Pastikan region_id ada
                            // Tambahkan dummy HSAs jika tidak ada
                            if (!newCity.hsas_detail || newCity.hsas_detail.length === 0) {
                                newCity.hsas_detail = [];
                                // Buat 1-3 dummy HSA per kota
                                const numHsas = Math.floor(Math.random() * 3) + 1;
                                for (let i = 0; i < numHsas; i++) {
                                    const hsaId = parseInt(`${newCity.id}0${i + 1}`); // ID dummy unik
                                    const hsaName = `HSA ${newCity.name} ${String.fromCharCode(65 + i)}`; // Contoh: HSA Jakarta A
                                    const hsaData = {
                                        id: hsaId,
                                        name: hsaName,
                                        city_id: newCity.id,
                                        k1: generateDummyKData(),
                                        k2: generateDummyKData(),
                                        k3: generateDummyKData(),
                                        stos_detail: [] // Akan diisi dummy STO
                                    };

                                    // Tambahkan dummy STOs ke HSA
                                    const numStos = Math.floor(Math.random() * 2) + 1;
                                    for (let j = 0; j < numStos; j++) {
                                        const stoId = parseInt(`${hsaId}0${j + 1}`); // ID dummy unik
                                        const stoName = `STO ${hsaName.replace('HSA ', '')} ${j + 1}`;
                                        hsaData.stos_detail.push({
                                            id: stoId,
                                            name: stoName,
                                            hsa_id: hsaId,
                                            k1: generateDummyKData(),
                                            k2: generateDummyKData(),
                                            k3: generateDummyKData()
                                        });
                                    }
                                    newCity.hsas_detail.push(hsaData);
                                }
                            } else {
                                // Jika sudah ada HSA dari backend, pastikan juga ada STO dummy
                                newCity.hsas_detail = newCity.hsas_detail.map(hsa => {
                                    const newHsa = { ...hsa, city_id: newCity.id }; // Pastikan city_id ada
                                    if (!newHsa.stos_detail || newHsa.stos_detail.length === 0) {
                                        newHsa.stos_detail = [];
                                        const numStos = Math.floor(Math.random() * 2) + 1;
                                        for (let j = 0; j < numStos; j++) {
                                            const stoId = parseInt(`${newHsa.id}0${j + 1}`);
                                            const stoName = `STO ${newHsa.name.replace('HSA ', '')} ${j + 1}`;
                                            newHsa.stos_detail.push({
                                                id: stoId,
                                                name: stoName,
                                                hsa_id: newHsa.id,
                                                k1: generateDummyKData(),
                                                k2: generateDummyKData(),
                                                k3: generateDummyKData()
                                            });
                                        }
                                    }
                                    return newHsa;
                                });
                            }
                            return newCity;
                        });
                    }
                    return newRegion;
                });
                // --- AKHIR MODIFIKASI ---


                elements.dashboardTableBody.innerHTML = ''; // Mengosongkan tabel
                if (processedData && processedData.length > 0) {
                    processedData.forEach(region => {
                        elements.dashboardTableBody.appendChild(createRow(region, 'region'));

                        if (region.cities_detail && region.cities_detail.length > 0) {
                            region.cities_detail.forEach(city => {
                                elements.dashboardTableBody.appendChild(createRow(city, 'city')); // city sudah punya region_id di dalamnya

                                if (city.hsas_detail && city.hsas_detail.length > 0) {
                                    city.hsas_detail.forEach(hsa => {
                                        elements.dashboardTableBody.appendChild(createRow(hsa, 'hsa')); // hsa sudah punya city_id

                                        if (hsa.stos_detail && hsa.stos_detail.length > 0) {
                                            hsa.stos_detail.forEach(sto => {
                                                elements.dashboardTableBody.appendChild(createRow(sto, 'sto')); // sto sudah punya hsa_id
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                } else {
                    elements.dashboardTableBody.innerHTML = '<tr><td colspan="25" class="text-center py-4">No data found for the selected filters.</td></tr>';
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
                elements.dashboardTableBody.innerHTML = `<tr><td colspan="25" class="text-center py-4 text-red-500">Failed to load data. Please try again. (${error.message})</td></tr>`;
            }
        }

        // --- Fungsi untuk Memuat Filter Wilayah dan Kota ---
        async function loadRegionCityFilters() {
            try {
                // Mengambil data dari API yang sudah ada
                const data = await fetchData('/api/regions-cities');
                if (data) {
                    allRegionsData = data;
                    elements.regionFilter.innerHTML = '<option value="">All Regions</option>';
                    elements.modalRegion.innerHTML = '<option value="">Pilih Regional</option>';
                    data.forEach(region => {
                        const option = new Option(region.name, region.id);
                        elements.regionFilter.add(option);
                        elements.modalRegion.add(option.cloneNode(true));
                    });
                }
            } catch (error) {
                console.error('Error loading region/city filters:', error);
                // Optionally show a message to the user
            }
        }

        function updateCityFilter(sourceSelect, targetSelect) {
            targetSelect.innerHTML = `<option value="">${targetSelect === elements.cityFilter ? 'All Cities' : 'Pilih Kota'}</option>`;
            const selectedRegionId = sourceSelect.value;
            if (selectedRegionId) {
                const selectedRegion = allRegionsData.find(r => r.id == selectedRegionId);
                if (selectedRegion?.cities) {
                    selectedRegion.cities.forEach(city => {
                        targetSelect.add(new Option(city.name, city.id));
                    });
                }
            }
        }

        // --- Penanganan Event (Event Listeners) ---

        // Dropdown menu pengguna
        elements.userMenuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            elements.userMenuDropdown.classList.toggle('hidden');
        });

        window.addEventListener('click', (event) => {
            if (!elements.userMenuDropdown.classList.contains('hidden')) {
                if (!elements.userMenuButton.contains(event.target) && !elements.userMenuDropdown.contains(event.target)) {
                    elements.userMenuDropdown.classList.add('hidden');
                }
            }
        });

        // Filter dashboard
        elements.applyFiltersBtn.addEventListener('click', loadDashboardData);
        elements.clearFiltersBtn.addEventListener('click', () => {
            elements.startDateInput.value = '';
            elements.endDateInput.value = '';
            elements.regionFilter.value = '';
            elements.categoryFilter.value = '';
            updateCityFilter(elements.regionFilter, elements.cityFilter);
            loadDashboardData();
        });

        elements.regionFilter.addEventListener('change', () => updateCityFilter(elements.regionFilter, elements.cityFilter));
        elements.modalRegion.addEventListener('change', () => updateCityFilter(elements.modalRegion, elements.modalCity));

        // Expand/Collapse Tabel Berjenjang
        elements.dashboardTableBody.addEventListener('click', (event) => {
            const clickedRow = event.target.closest('tr[data-level][data-expandable="true"]'); // Hanya proses jika expandable

            if (clickedRow) {
                const level = clickedRow.dataset.level;
                const icon = clickedRow.querySelector('i.fa-chevron-right');

                if (icon) {
                    icon.classList.toggle('rotate-90');
                }

                let targetSelector = '';
                let parentId = '';

                switch (level) {
                    case 'region':
                        parentId = clickedRow.dataset.regionId;
                        targetSelector = `tr[data-parent-id="${parentId}"][data-level="city"]`;
                        break;
                    case 'city':
                        parentId = clickedRow.dataset.cityId;
                        targetSelector = `tr[data-parent-id="${parentId}"][data-level="hsa"]`;
                        break;
                    case 'hsa':
                        parentId = clickedRow.dataset.hsaId;
                        targetSelector = `tr[data-parent-id="${parentId}"][data-level="sto"]`;
                        break;
                }

                if (targetSelector) {
                    document.querySelectorAll(targetSelector).forEach(row => {
                        row.classList.toggle('hidden');

                        // Jika baris anak disembunyikan, sembunyikan semua cucunya juga (rekursif)
                        if (row.classList.contains('hidden')) {
                            let childrenTargetSelector = '';
                            let currentLevelId = '';

                            if (row.dataset.level === 'city') {
                                currentLevelId = row.dataset.cityId;
                                childrenTargetSelector = `tr[data-parent-id="${currentLevelId}"][data-level="hsa"]`;
                            } else if (row.dataset.level === 'hsa') {
                                currentLevelId = row.dataset.hsaId;
                                childrenTargetSelector = `tr[data-parent-id="${currentLevelId}"][data-level="sto"]`;
                            }

                            if (childrenTargetSelector) {
                                document.querySelectorAll(childrenTargetSelector).forEach(nestedRow => {
                                    nestedRow.classList.add('hidden');
                                    const nestedIcon = nestedRow.querySelector('i.fa-chevron-right');
                                    if (nestedIcon) {
                                        nestedIcon.classList.remove('rotate-90');
                                    }
                                    hideNestedChildren(nestedRow);
                                });
                            }
                        }
                    });
                }
            }
        });

        // Fungsi bantu untuk menyembunyikan anak-anak lebih dalam secara rekursif
        function hideNestedChildren(parentRow) {
            let currentLevel = parentRow.dataset.level;
            let currentId = '';
            let nextLevelSelector = '';

            if (currentLevel === 'city') {
                currentId = parentRow.dataset.cityId;
                nextLevelSelector = `tr[data-parent-id="${currentId}"][data-level="hsa"]`;
            } else if (currentLevel === 'hsa') {
                currentId = parentRow.dataset.hsaId;
                nextLevelSelector = `tr[data-parent-id="${currentId}"][data-level="sto"]`;
            }

            if (nextLevelSelector) {
                document.querySelectorAll(nextLevelSelector).forEach(childRow => {
                    childRow.classList.add('hidden');
                    const childIcon = childRow.querySelector('i.fa-chevron-right');
                    if (childIcon) {
                        childIcon.classList.remove('rotate-90');
                    }
                    hideNestedChildren(childRow);
                });
            }
        }

        // Modal "Add Data"
        elements.addDataBtn.addEventListener('click', () => {
            elements.addDataModal.classList.remove('hidden');
            elements.modalErrors.innerHTML = '';
            elements.addDataForm.reset();
            document.getElementById('modalEntryDate').value = new Date().toISOString().split('T')[0];
            updateCityFilter(elements.modalRegion, elements.modalCity);
        });

        elements.closeModalBtn.addEventListener('click', () => elements.addDataModal.classList.add('hidden'));

        window.addEventListener('click', (event) => {
            if (event.target === elements.addDataModal) {
                elements.addDataModal.classList.add('hidden');
            }
        });

        elements.addDataForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = Object.fromEntries(new FormData(elements.addDataForm).entries());
            formData.city_id = parseInt(formData.city_id, 10);
            elements.modalErrors.innerHTML = '';

            try {
                // Panggil postData ke API Laravel Anda
                const response = await postData('/api/dashboard-data', formData);

                if (response.message && !response.errors) {
                    alert(response.message);
                    elements.addDataModal.classList.add('hidden');
                    loadDashboardData(); // Reload data setelah penambahan
                } else if (response.errors) {
                    elements.modalErrors.innerHTML = `<ul>${Object.values(response.errors).flat().map(e => `<li>${e}</li>`).join('')}</ul>`;
                } else {
                    alert('An unknown error occurred.');
                }
            } catch (error) {
                alert('An error occurred. Check the console.');
                console.error('Submit Error:', error);
            }
        });

        // --- Panggilan Inisial ---
        loadRegionCityFilters();
        loadDashboardData();

    } catch (e) {
        console.error("A critical error occurred during script initialization:", e);
        alert("A JavaScript error occurred: " + e.message + ". Check the console for more details.");
    }
});