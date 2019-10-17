class OpponentHand {
    constructor(z_fighter, cards, selector, card_width, card_height, card_separation, angle, cards_offset_x, cards_offset_y) {
        this.hand_container = new HandContainer(cards, selector, this.createCardElement);
        this.card_fan = new Fan(this.hand_container, card_separation, card_width, card_height, true, cards_offset_y, cards_offset_x + (card_width / 2), true, angle, z_fighter, false);
    }

    createCardElement(color, index) {
        return $(`<div class="card opponent ${CardHelper.createColorFromColorId(color)}"></div>`);
    }

    initialize() {
        this.initializeCards();
    }

    initializeCards() {
        this.card_fan.positionCards(true);
        this.hand_container.show();
    }

    getHandContainer() {
        return this.hand_container;
    }

    addCards(cards) {
        this.hand_container.addCards(cards);
        this.initializeCards();
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