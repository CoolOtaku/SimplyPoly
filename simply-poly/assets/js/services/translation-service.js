export default class TranslationService {
    async save(data) {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'simplypoly_save_translation',
                nonce: simplypoly.nonce,
                translations: JSON.stringify(data)
            })
        });

        return response.json();
    }
}