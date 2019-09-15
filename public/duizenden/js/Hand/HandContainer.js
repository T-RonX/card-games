class HandContainer {
    constructor(cards, selector) {
        this.cards = cards;
        this.container = $(selector);
        this.card_elems = [];
        this.createCards();
    }

    createCards() {
        let index = 0;
        for (const card of this.cards) {
            const card_elem = this.createCard(card, ++index);
            this.card_elems.push(card_elem);
            this.container.append(card_elem);
        }
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

    getCards() {
        return this.card_elems
    }

    getContainer() {
        return this.container;
    }

    addCard(card) {
        this.cards.push(card);
        const card_elem = this.createCard(card, this.cards.length);
        this.card_elems.push(card_elem);
        this.container.append(card_elem);
    }
}