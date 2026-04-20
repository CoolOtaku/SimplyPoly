export default class TranslationService {
    async load() {
        const response = await fetch(simplypoly.ajaxurl, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'simplypoly_get_translations',
                nonce: simplypoly.nonce_get,
                post_id: simplypoly.post_id
            })
        });

        const result = await response.json();

        if (!result.success) throw new Error(result.data?.message || 'Load failed');

        return result.data.data;
    }

    async save(translations) {
        const response = await fetch(simplypoly.ajaxurl, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'simplypoly_post_translation',
                nonce: simplypoly.nonce_post,
                post_id: simplypoly.post_id,
                translations: JSON.stringify(translations)
            })
        });

        const result = await response.json();

        if (!result.success) throw new Error(result.data?.message || 'Save failed');

        return result.data;
    }
}