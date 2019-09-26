class HandContainer {
    constructor(cards, selector) {
        this.cards = cards;
        this.container = $(selector);
        this.card_elems = [];
        this.createCards();
    }

    createCards() {
        this.card_elems = [];
        this.container.empty();

        let index = 0;
        for (const card of this.cards) {
            const card_elem = this.createCard(card, ++index);
            this.card_elems.push(card_elem);
            this.container.append(card_elem);
        }
    }

    setCards(cards) {
        this.cards = cards;
    }

    createCard(identifier, index) {
        return $(`<div data-card-order="${index}" data-card-id="${identifier}" class="card hand ${identifier} draggable"></div>`);
    }

    clear() {
        this.container.empty();
    }

    show() {
        this.container.fadeIn(200);
    }

    getCards(plain = false) {
        return plain ? this.cards : this.card_elems;
    }

    getContainer() {
        return this.container;
    }

    addCards(cards) {
        for (const card of Array.isArray(cards) ? cards : [cards]) {
            this.cards.concat();
            const card_elem = this.createCard(card, this.cards.length);
            this.card_elems.push(card_elem);
            this.container.append(card_elem);
        }
    }

    reorder(source, target) {
        if (source === target) {
            return;
        }

        if (!source in this.cards || (target < -1 && !target in this.cards)) {
            return;
        }

        const moving_card = this.cards.splice(source, 1)[0];

        if (target === -1) {
            this.cards.unshift(moving_card);
        } else {
            this.cards.splice(source > target ? target + 1 : target, 0, moving_card);
        }
    }
}