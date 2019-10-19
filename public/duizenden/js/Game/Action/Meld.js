class Melds {
    static createMelds(player_id, zfighter, melds, container, card_width, card_height, card_separation, path_extend_meld) {
        let unique = 0;
        for (const meld of melds) {
            this.createMeld(player_id, zfighter, meld, container, card_width, card_height, card_separation, path_extend_meld, ++unique);
        }
    }

    static createMeld(player_id, zfighter, meld, container, card_width, card_height, card_separation, path_extend_meld, unique) {
        const meld_container = $(`<div class="meld_container" data-meld-unique="${player_id + '_' + unique}" data-meld-id="${unique}"></div>`);
        const m = new Meld(zfighter, meld.cards.cards, meld_container, card_width, card_height, card_separation, path_extend_meld, 0);
        m.initialize();
        container.append(meld_container);
    }
}