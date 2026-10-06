/**
 * ZAMEDU · SIGE - Cascata Territorial Dinâmica MINEDH
 * Província -> Distrito -> ZIP -> Escola
 * Suporta Barras de Filtro e Modais de Registo/Edição com integridade estrita.
 */
(function() {
    'use strict';

    function setupCascadeForForm(form) {
        if (!form || form.dataset.cascadeInitialized === 'true') return;

        const provinceSelect = form.querySelector('select[name="province_id"], select#filter_province_id, select#school_province_id, select#district_province_id, select#zip_province_id, select#map_province_id');
        const districtSelect = form.querySelector('select[name="district_id"], select#filter_district_id, select#school_district_id, select#zip_district_id, select#map_district_id');
        const zipSelect = form.querySelector('select[name="zip_id"], select#filter_zip_id, select#school_zip_id, select#map_zip_id');
        const schoolSelect = form.querySelector('select[name="school_id"], select#filter_school_id, select#school_select');

        if (!provinceSelect || !districtSelect) return;

        form.dataset.cascadeInitialized = 'true';

        // Detectar se é modo formulário de registo (requer preenchimento estrito) ou barra de filtros
        const isRegistrationForm = districtSelect.required || form.id.startsWith('form') || form.id.includes('wizard');

        const districtDefaultText = isRegistrationForm ? 'Selecione o Distrito...' : 'Todos os Distritos';
        const zipDefaultText = isRegistrationForm ? (zipSelect && zipSelect.required ? 'Selecione a ZIP...' : 'Nenhuma ZIP (Opcional)') : 'Todas as ZIPs';
        const schoolDefaultText = isRegistrationForm ? 'Selecione a Escola...' : 'Todas as Escolas';

        // 1. Cache inicial de opções de Distritos
        const rawDistrictOptions = Array.from(districtSelect.options);
        const cachedDistricts = [];
        rawDistrictOptions.forEach(opt => {
            if (opt.value) {
                cachedDistricts.push({
                    value: opt.value,
                    text: opt.text,
                    provinceId: opt.dataset.province ? String(opt.dataset.province) : null
                });
            }
        });
        districtSelect._cachedOptions = cachedDistricts;

        // 2. Cache inicial de opções de ZIPs
        const cachedZips = [];
        if (zipSelect) {
            Array.from(zipSelect.options).forEach(opt => {
                if (opt.value) {
                    cachedZips.push({
                        value: opt.value,
                        text: opt.text,
                        districtId: opt.dataset.district ? String(opt.dataset.district) : null
                    });
                }
            });
            zipSelect._cachedOptions = cachedZips;
        }

        // 3. Cache inicial de opções de Escolas (se houver)
        const cachedSchools = [];
        if (schoolSelect) {
            Array.from(schoolSelect.options).forEach(opt => {
                if (opt.value) {
                    cachedSchools.push({
                        value: opt.value,
                        text: opt.text,
                        provinceId: opt.dataset.province ? String(opt.dataset.province) : null,
                        districtId: opt.dataset.district ? String(opt.dataset.district) : null
                    });
                }
            });
            schoolSelect._cachedOptions = cachedSchools;
        }

        // Função para filtrar Distritos com base na Província
        async function updateDistricts(targetProvId, preserveDistId = null) {
            const currentDistValue = preserveDistId !== null ? preserveDistId : districtSelect.value;
            districtSelect.innerHTML = '';

            // Se for formulário de registo e não houver província, desabilitar e indicar
            if (isRegistrationForm && !targetProvId) {
                districtSelect.disabled = true;
                districtSelect.add(new Option('Selecione a Província primeiro...', ''));
                if (zipSelect) {
                    zipSelect.disabled = true;
                    zipSelect.innerHTML = '';
                    zipSelect.add(new Option('Selecione o Distrito primeiro...', ''));
                }
                return;
            }

            districtSelect.disabled = false;
            districtSelect.add(new Option(districtDefaultText, ''));

            if (!targetProvId) {
                // Em filtro sem província: mostra todos os distritos disponíveis
                cachedDistricts.forEach(d => {
                    const opt = new Option(d.text, d.value);
                    if (d.provinceId) opt.dataset.province = d.provinceId;
                    districtSelect.add(opt);
                });
                return;
            }

            // Filtrar distritos locais que pertencem à província
            const matchingDistricts = cachedDistricts.filter(d => !d.provinceId || d.provinceId === String(targetProvId));

            if (matchingDistricts.length > 0) {
                matchingDistricts.forEach(d => {
                    const opt = new Option(d.text, d.value);
                    if (d.provinceId) opt.dataset.province = d.provinceId;
                    if (String(d.value) === String(currentDistValue)) {
                        opt.selected = true;
                    }
                    districtSelect.add(opt);
                });
            } else {
                // Fallback API se o cache não possuir mapeamento
                try {
                    const res = await fetch(`/api/v1/territory/districts?province_id=${targetProvId}`);
                    const json = await res.json();
                    if (json.status === 'success' && Array.isArray(json.data)) {
                        json.data.forEach(d => {
                            const opt = new Option(d.name, d.id);
                            opt.dataset.province = String(targetProvId);
                            if (String(d.id) === String(currentDistValue)) {
                                opt.selected = true;
                            }
                            districtSelect.add(opt);
                        });
                    }
                } catch (e) {
                    console.warn('Cascata Territorial: Falha ao carregar distritos via API:', e);
                }
            }

            // Atualiza ZIPs com o distrito selecionado ou limpa
            if (zipSelect) {
                await updateZips(districtSelect.value);
            }
        }

        // Função para filtrar ZIPs com base no Distrito
        async function updateZips(targetDistId, preserveZipId = null) {
            if (!zipSelect) return;

            const currentZipValue = preserveZipId !== null ? preserveZipId : zipSelect.value;
            zipSelect.innerHTML = '';

            if (isRegistrationForm && !targetDistId) {
                zipSelect.disabled = true;
                zipSelect.add(new Option('Selecione o Distrito primeiro...', ''));
                return;
            }

            zipSelect.disabled = false;
            zipSelect.add(new Option(zipDefaultText, ''));

            if (!targetDistId) {
                if (!isRegistrationForm) {
                    cachedZips.forEach(z => {
                        const opt = new Option(z.text, z.value);
                        if (z.districtId) opt.dataset.district = z.districtId;
                        zipSelect.add(opt);
                    });
                }
                return;
            }

            const matchingZips = cachedZips.filter(z => !z.districtId || z.districtId === String(targetDistId));

            if (matchingZips.length > 0) {
                matchingZips.forEach(z => {
                    const opt = new Option(z.text, z.value);
                    if (z.districtId) opt.dataset.district = z.districtId;
                    if (String(z.value) === String(currentZipValue)) {
                        opt.selected = true;
                    }
                    zipSelect.add(opt);
                });
            } else {
                try {
                    const res = await fetch(`/api/v1/territory/zips?district_id=${targetDistId}`);
                    const json = await res.json();
                    if (json.status === 'success' && Array.isArray(json.data)) {
                        json.data.forEach(z => {
                            const opt = new Option(z.name, z.id);
                            opt.dataset.district = String(targetDistId);
                            if (String(z.id) === String(currentZipValue)) {
                                opt.selected = true;
                            }
                            zipSelect.add(opt);
                        });
                    }
                } catch (e) {
                    console.warn('Cascata Territorial: Falha ao carregar ZIPs via API:', e);
                }
            }
        }

        // Eventos de escuta
        provinceSelect.addEventListener('change', function() {
            updateDistricts(this.value);
        });

        districtSelect.addEventListener('change', function() {
            if (zipSelect) {
                updateZips(this.value);
            }
        });

        // Sincronização inicial no carregamento se houver província ou distrito pré-selecionado
        const initialProv = provinceSelect.value;
        const initialDist = districtSelect.value;
        const initialZip = zipSelect ? zipSelect.value : null;

        if (initialProv) {
            updateDistricts(initialProv, initialDist).then(() => {
                if (initialDist && zipSelect) {
                    updateZips(initialDist, initialZip);
                }
            });
        } else if (isRegistrationForm) {
            // Se for formulário de registo e estiver vazio, bloqueia distrito e zip
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Selecione a Província primeiro...</option>';
            if (zipSelect) {
                zipSelect.disabled = true;
                zipSelect.innerHTML = '<option value="">Selecione o Distrito primeiro...</option>';
            }
        }

        // Guardar métodos de sincronização no formulário para chamadas externas
        form._updateTerritoryCascade = async function(data) {
            if (data.provinceId !== undefined) {
                provinceSelect.value = data.provinceId || '';
                await updateDistricts(data.provinceId, data.districtId || null);
            }
            if (data.districtId !== undefined && districtSelect) {
                districtSelect.value = data.districtId || '';
                if (zipSelect) {
                    await updateZips(data.districtId, data.zipId || null);
                }
            }
            if (data.zipId !== undefined && zipSelect) {
                zipSelect.value = data.zipId || '';
            }
        };

        form._resetTerritoryCascade = function() {
            provinceSelect.value = '';
            if (isRegistrationForm) {
                districtSelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Selecione a Província primeiro...</option>';
                if (zipSelect) {
                    zipSelect.disabled = true;
                    zipSelect.innerHTML = '<option value="">Selecione o Distrito primeiro...</option>';
                }
            } else {
                updateDistricts('');
                if (zipSelect) updateZips('');
            }
        };
    }

    function initTerritoryCascade(container = document) {
        const forms = container.querySelectorAll('form');
        forms.forEach(form => setupCascadeForForm(form));
    }

    // Helper global para sincronizar formulários em modais
    window.syncTerritoryModal = function(formOrId, data) {
        const form = typeof formOrId === 'string' ? document.getElementById(formOrId) : formOrId;
        if (!form) return;
        if (!form.dataset.cascadeInitialized) {
            setupCascadeForForm(form);
        }
        if (typeof form._updateTerritoryCascade === 'function') {
            form._updateTerritoryCascade(data);
        }
    };

    // Helper global para resetar formulários de território
    window.resetTerritoryForm = function(formOrId) {
        const form = typeof formOrId === 'string' ? document.getElementById(formOrId) : formOrId;
        if (!form) return;
        if (!form.dataset.cascadeInitialized) {
            setupCascadeForForm(form);
        }
        if (typeof form._resetTerritoryCascade === 'function') {
            form._resetTerritoryCascade();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initTerritoryCascade());
    } else {
        initTerritoryCascade();
    }

    window.initTerritoryCascade = initTerritoryCascade;
})();
