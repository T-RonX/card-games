class OpponentHand {
    constructor(z_fighter, cards, selector, card_width, card_height, card_separation, angle, cards_offset_x, cards_offset_y) {
        this.hand_container = new HandContainer(cards, selector, this.createCardElement);
        this.card_fan = new Fan(this.hand_container, card_separation, card_width, card_height, true, 0, Math.ceil(card_width / 2), true, angle, z_fighter, false, false);
    }

    createCardElement(id, index) {
        return $(`<div class="card opponent ${CardHelper.getValueOrColorFromId(id)}"></div>`);
    }

    initialize() {
        this.initializeCards();
    }

    initializeCards() {
        this.card_fan.positionCards();
        this.hand_container.show();
    }

    getHandContainer() {
        return this.hand_container;
    }

    addCards(cards, target) {
        this.hand_container.addCards(cards, target);
        this.initializeCards();
    }

    redraw(card_width, card_height) {
        return this.card_fan.redraw(card_width, card_height);
    }

    redrawCards() {
        this.hand_container.createCards();
        this.initializeCards();
    }

    reorderCards(from, to) {
        this.hand_container.reorder(from - 1, to - 1);
        this.redrawCards();
    }
}