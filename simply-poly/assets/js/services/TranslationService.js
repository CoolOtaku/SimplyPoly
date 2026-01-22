export default class TranslationService {
    async save(postId, lang, text) {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'simplypoly_save_translation',
                nonce: simplypoly.nonce,
                post_id: postId,
                lang: lang,
                text: text
            })
        });

        return response.json();
    }
}