class Meld {
    constructor(z_fighter, cards, container, card_width, card_height, card_separation, path_extend_meld, angle_offset) {
        this.card_separation = card_separation;
        this.meld_container = new MeldContainer(cards, container, card_width, card_height, card_separation);
        this.card_fan = new Fan(this.meld_container, card_separation, card_width, card_height, 0, 0, -this.getOffsetX(card_width), true, angle_offset, z_fighter, false, false);
        this.dropable_hand = new DroppableMeld(this.meld_container.getContainer(), path_extend_meld);
    }

    initialize() {
        this.card_fan.positionCards();
        this.dropable_hand.makeDropable();
        this.meld_container.show();
    }

    getOffsetX(card_width) {
        return ((((this.meld_container.getCards().length - 1) * (card_width * this.card_separation)) ) / 2) * 1.01;
    }

    redraw(card_width, card_height) {
        this.meld_container.redraw(card_width, card_height);
        this.card_fan.redraw(card_width, card_height, 0, -this.getOffsetX(card_width));
    }
}