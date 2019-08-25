class Game
{
    constructor(event_handler, hand) {
        this.hand = hand;
        this.event_handler = event_handler;
    }

    static create(connection, event_handler, selector, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation) {
        const z_fighter = new ZFighter(100);
        const hand = new Hand(z_fighter, selector, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation);

        return new Game(event_handler, hand);
    }

    initialize() {
        this.hand.initialize();
    }
}