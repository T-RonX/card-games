class MeldContainer {
    constructor(cards, container, card_width, card_height, card_separation) {
        this.cards = cards;
        this.container = container;
        this.card_count = this.cards.length;
        this.card_width = card_width;
        this.card_height = card_height;
        this.card_separation = card_separation;
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

    createCard(identifier) {
        return $(`<div data-card-id="${identifier}" class="card table ${CardHelper.getValueFromId(identifier)}"></div>`);
    }

    clear() {
        this.container.empty();
    }

    redraw(card_width, card_height) {
        this.card_width = card_width;
        this.card_height = card_height;
        this.show()
    }

    show() {
        const width = ((((this.card_count - 1) * this.card_width) * this.card_separation) + this.card_width) * 1.015;
        const height = this.card_height + this.card_height * .03;
        this.container.css('width', width + 'px');
        this.container.css('height', height + 'px');
        this.container.fadeIn(200);
    }

    getCards() {
        return this.card_elems
    }

    getContainer() {
        return this.container;
    }
}