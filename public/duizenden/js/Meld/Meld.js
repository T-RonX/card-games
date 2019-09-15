class Meld {
    constructor(z_fighter, card_selector, card_width, card_height, card_separation, path_extend_meld) {
        this.z_fighter = z_fighter;
        this.meld_container = new MeldContainer(card_selector, card_width, card_height, card_separation);
        this.cards = $(card_selector);

        const offset_y = card_height * 0.53;
        const offset_x = ((((this.cards.length - 1) * (card_width * card_separation)) + card_width) / 2) * 1.01;

        this.card_fan = new Fan(this.cards, card_separation, card_width, card_height, false, offset_y, offset_x, 1);
        this.dropable_hand = new DroppableMeld(this.meld_container.getContainer(), path_extend_meld);
    }

    initialize() {
        this.card_fan.positionCards(false);
        this.dropable_hand.makeDropable();
        this.meld_container.show();
    }
}