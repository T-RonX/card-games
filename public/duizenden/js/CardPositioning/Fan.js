class Fan {
    constructor(card_container, card_overlap, card_width, card_height, add_random_deviation, offset_y, offset_x, start_z_index) {
        this.card_container = card_container;
        this.card_overlap = card_overlap;
        this.card_width = card_width;
        this.card_height = card_height;
        this.add_random_deviation = add_random_deviation;
        this.offset_y = offset_y;
        this.offset_x = offset_x;
        this.zindex = start_z_index;
    }

    setup() {
        this.card_count = this.card_container.getCards().length;
        let radius_increase_ratio = 10 * this.card_count; // increase the radius according to the card count
        this.radius = Math.max(1800, radius_increase_ratio * this.card_count);
        let center_y_offset = this.radius - this.offset_y;
        this.center_y = center_y_offset + Math.floor(this.card_height / 2);
        this.center_x = (-Math.floor(this.card_width / 2)) + this.offset_x;
        let circumference = 2 * Math.PI * this.radius;
        this.separation_angle = this.card_width * this.card_overlap / (circumference / 360);
        this.angle = -90 - (((this.separation_angle * this.card_count) / 2) - (this.separation_angle / 2));
        this.card_positions = [];
    }

    positionCards(add_dropper) {
        this.setup();
        let i = 0;

        for (const card of this.card_container.getCards()) {
            let add_deviation = this.add_random_deviation && this.card_count !== 1;
            let coords = this.getCoordinates(this.angle, add_deviation);
            let rotate = this.getRotation(coords.x, coords.y, add_deviation);
            this.card_positions[i] = { x: coords.x, y: coords.y, rotate: rotate };

           // let card = $(element);
            card.data('rotation_angle', rotate);
            card.css('position', 'absolute');
            card.css('left', coords.x + 'px');
            card.css('top', coords.y + 'px');
            card.css('z-index', this.zindex);
            card.css('transform', 'rotate(' + rotate + 'deg)');

            ++this.zindex;
            this.angle += this.separation_angle;
            ++i;
        }

        if (add_dropper) {
            this.addDroppableArea();
        }
    }

    getCoordinates(angle, add_deviation) {
        let x = (this.radius * Math.cos(MathHelper.degreesToRadians(angle))) + this.center_x;
        let y = (this.radius * Math.sin(MathHelper.degreesToRadians(angle))) + this.center_y;

        let div_x, div_y;

        if (add_deviation) {
            let div = 2;
            div_x = Math.floor(Math.random() * (2 * div)) - div;
            div_y = Math.floor(Math.random() * (2 * div)) - div;

            x += div_x;
            y += div_y;
        }

        return {x: x, y: y};
    }

    getRotation(x, y, add_deviation) {
        let rotate = MathHelper.radiansToDegrees(Math.atan2(y - this.center_y, x - this.center_x)) + 90;
        let div_rotate;

        if (add_deviation) {
            let div_angle = 5;
            div_rotate = (Math.floor(Math.random() * (2 * div_angle + 1)) - div_angle) / 10;
            rotate += div_rotate;
        }

        return rotate;
    }

    addDroppableArea() {
        let container = this.card_container.getContainer();
        this.zindex += this.card_count;

        this.angle = -90 - (((this.separation_angle * this.card_count) / 2) - (this.separation_angle / 2));
        this.angle  -= this.separation_angle;
        let coords = this.getCoordinates(this.angle, false);
        let rotate = this.getRotation(coords.x, coords.y, false);
        let n = 0;

        this.addDropper(container, 0, coords.x, coords.y, rotate, this.zindex, false);

        for (let [i, card_position] of this.card_positions.entries())
        {
            this.addDropper(container, ++n, card_position.x, card_position.y, card_position.rotate, this.zindex, i === this.card_count - 1);
            ++this.zindex;
        }
    }

    addDropper(container, id, x, y, rotate, zindex, is_last) {
        let dropper = $('<div class="card_hand_dropper"></div>');
        let indicator = $('<div class="card_hand_dropper_indicator"></div>');

        dropper.css('position', 'absolute');
        dropper.css('left', x + 'px');
        dropper.css('top', y + 'px');
        dropper.css('z-index', zindex);
        dropper.css('transform', 'rotate(' + rotate + 'deg)');
        dropper.css('transform-origin', (Math.floor(this.card_width / 2)) + 'px ' + (Math.floor(this.card_height / 2)) +  'px');
        dropper.css('width', (is_last ? this.card_width + 1  : ((this.card_width * this.card_overlap))) + 'px');
        dropper.css('height', this.card_height + 'px');
        dropper.data('card-order', id);

        indicator.css('width',  (this.card_width * .05) + 'px');
        indicator.css('height', this.card_height + 'px');
        dropper.append(indicator);
        container.append(dropper);
    }
}