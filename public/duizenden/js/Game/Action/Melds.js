class Melds {
    static createMelds(player_id, zfighter, melds, container, card_width, card_height, card_separation, path_extend_meld, offset_angle) {
        let unique = 0;
        for (const meld of melds) {
            this.createMeld(player_id, zfighter, meld, container, card_width, card_height, card_separation, path_extend_meld, offset_angle, ++unique);
        }
    }

    static createMeld(player_id, zfighter, meld, container, card_width, card_height, card_separation, path_extend_meld, offset_angle, unique) {
        const meld_container = $(`<div class="meld_container" data-meld-unique="${player_id + '_' + unique}" data-meld-id="${unique}"></div>`);
        const m = new Meld(zfighter, meld.cards.cards, meld_container, card_width, card_height, card_separation, path_extend_meld, offset_angle);
        m.initialize();
        container.append(meld_container);
    }

    static extendMeld(player_id, meld_id, meld, container, card_width, card_height, card_separation, path_extend_meld, offset_angle) {
        $(`.meld_container[data-meld-unique=${player_id}_${meld_id}]`).remove();
        this.createMeld(player_id, new ZFighter(1), meld,container, card_width, card_height, card_separation, path_extend_meld, offset_angle);
    }
}