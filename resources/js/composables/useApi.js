import { ref } from 'vue';

export function useFetch(url) {
    const data = ref(null);
    const loading = ref(false);
    const error = ref(null);

    async function load() {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`${response.status} ${response.statusText}`);
            }

            data.value = await response.json();
        } catch (e) {
            error.value = e.message ?? 'Не удалось загрузить данные';
        } finally {
            loading.value = false;
        }
    }

    load();

    return { data, loading, error, reload: load };
}

export function usePaginatedFetch(url) {
    const items = ref([]);
    const page = ref(1);
    const lastPage = ref(1);
    const total = ref(0);
    const loading = ref(false);
    const error = ref(null);

    async function load() {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(`${url}?page=${page.value}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`${response.status} ${response.statusText}`);
            }

            const json = await response.json();
            items.value = json.data;
            lastPage.value = json.last_page;
            total.value = json.total;
        } catch (e) {
            error.value = e.message ?? 'Не удалось загрузить данные';
        } finally {
            loading.value = false;
        }
    }

    function goToPage(target) {
        if (target < 1 || target > lastPage.value || target === page.value) return;
        page.value = target;
        load();
    }

    load();

    return { items, page, lastPage, total, loading, error, reload: load, goToPage };
}
