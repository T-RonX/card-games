class Meld {
    constructor(z_fighter, cards, container, card_width, card_height, card_separation, path_extend_meld, angle_offset) {
        this.meld_container = new MeldContainer(cards, container, card_width, card_height, card_separation);

        const offset_y = 0;
        const offset_x = ((((this.meld_container.getCards().length - 1) * (card_width * card_separation)) ) / 2) * 1.01;

        this.card_fan = new Fan(this.meld_container, card_separation, card_width, card_height, false, offset_y, -offset_x, true, angle_offset, z_fighter, false);
        this.dropable_hand = new DroppableMeld(this.meld_container.getContainer(), path_extend_meld);
    }

    initialize() {
        this.card_fan.positionCards(false);
        this.dropable_hand.makeDropable();
        this.meld_container.show();
    }
}