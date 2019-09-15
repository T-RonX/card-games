class Game {
    constructor(event_handler, hand, meld_containers) {
        this.event_handler = event_handler;
        this.meld_containers = meld_containers;
        this.hand = hand;
    }

    static create(
        connection,
        event_handler,
        hand_container_selector,
        meld_container_card_selector,
        path_draw_from_discarded,
        path_draw_from_undrawn,
        card_width,
        card_height,
        card_width_meld,
        card_height_meld,
        card_separation,
        card_separation_meld,
        path_extend_meld) {

        const z_fighter = new ZFighter(100);
        const hand = new Hand(z_fighter, hand_container_selector, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation);

        const melds_containers = $("[data-meld-id]");
        let melds = [];

        melds_containers.each((i, element) => {
            const container = $(element);
            let container_unique = container.data('meld-unique');
            melds.push(new Meld(
                z_fighter,
                `[data-meld-unique="${container_unique}"] ${meld_container_card_selector}`,
                card_width_meld,
                card_height_meld,
                card_separation_meld,
                path_extend_meld
            ));
        });

        return new Game(event_handler, hand, melds);
    }

    initialize() {
        for (const meld of this.meld_containers) {
            meld.initialize();
        }

        this.hand.initialize();
    }
}