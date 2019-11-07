class Melds {
    static createMelds(player_id, zfighter, melds, container, card_width, card_height, card_separation, path_extend_meld, offset_angle) {
        let unique = 0;
        for (const meld of melds) {
            this.createMeld(player_id, zfighter, meld.cards.cards, container, card_width, card_height, card_separation, path_extend_meld, offset_angle, ++unique);
        }
    }

    static createMeld(player_id, zfighter, cards, container, card_width, card_height, card_separation, path_extend_meld, offset_angle, unique, prepend = false) {
        const meld_container = $(`<div class="meld_container" data-meld-unique="${player_id + '_' + unique}" data-meld-id="${unique}"></div>`);
        this.updateMeldContainer(zfighter, cards, meld_container, card_width, card_height, card_separation, path_extend_meld, offset_angle);
        if (prepend) {
            container.prepend(meld_container);
        } else {
            container.append(meld_container);
        }
    }

    static extendMeld(player_id, meld_id, cards, card_width, card_height, card_separation, path_extend_meld, offset_angle) {
        const container = $(`.meld_container[data-meld-unique=${player_id}_${meld_id}]`);
        container.empty();
        this.updateMeldContainer(new ZFighter(1), cards, container, card_width, card_height, card_separation, path_extend_meld, offset_angle);
    }

    static updateMeldContainer(zfighter, cards, meld_container, card_width, card_height, card_separation, path_extend_meld, offset_angle) {
        const m = new Meld(zfighter, cards, meld_container, card_width, card_height, card_separation, path_extend_meld, offset_angle);
        m.initialize();
    }

    static removeMelds(player_id = null) {
        if (null == player_id) {
            $('.meld_container').remove();
            return [];
        } else {
            let cards = this.getCardIdsFromMeld(player_id);
            $(`.meld_container[data-meld-unique^=${player_id}_]`).remove();
            return cards;
        }
    }

    static getCardIdsFromMeld(player_id) {
        let cards = [];
        $(`.meld_container[data-meld-unique^=${player_id}_] [data-card-id]`).each((i, e) => {
            cards.push($(e).data('card-id'));
        });

        return cards;
    }
}