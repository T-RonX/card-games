class Fan {
    constructor(card_container, card_overlap, card_width, card_height, random_deviation, offset_y, offset_x, offset_zero_out, offset_angle, z_fighter, reverse_stacking, add_dropper) {
        this.card_container = card_container;
        this.card_overlap = card_overlap;
        this.card_width = card_width;
        this.card_height = card_height;
        this.random_deviation = random_deviation;
        this.offset_y = offset_y;
        this.offset_x = offset_x;
        this.offset_zero_out = offset_zero_out;
        this.offset_angle = offset_angle;
        this.z_fighter = z_fighter;
        this.reverse_stacking = reverse_stacking;
        this.zero_out_x_avg = 0;
        this.zero_out_y_avg = 0;
        this.add_dropper = add_dropper;
        this.card_offsets = [];
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
        this.angle = (-90 + this.offset_angle) - (((this.separation_angle * this.card_count) / 2) - (this.separation_angle / 2));
        this.card_positions = [];
    }

    redraw(card_width, card_height, offset_y, offset_x) {
        this.card_width = card_width;
        this.card_height = card_height;
        this.offset_y = offset_y;
        this.offset_x = offset_x;

        this.positionCards();
    }

    positionCards() {
        this.setup();
        let i = 0;

        if (this.reverse_stacking) {
            this.z_fighter.up(this.card_container.getCards().length);
        }

        for (const card of this.card_container.getCards()) {
            const add_deviation = this.random_deviation && this.card_count !== 1;
            const coords = this.getCoordinates(this.angle, add_deviation, i);
            const rotate = this.getRotation(coords.x, coords.y, add_deviation, i);
            this.card_positions[i] = { x: coords.x, y: coords.y, rotate: rotate.rotate };
            this.card_offsets[i] = {offset_x: coords.offset_x, offset_y: coords.offset_y, offset_angle: rotate.offset_angle};

            card.data('rotation_angle', rotate.rotate);
            card.css('position', 'absolute');
            card.css('left', coords.x + 'px');
            card.css('top', coords.y + 'px');
            card.css('z-index', this.reverse_stacking ? this.z_fighter.down() : this.z_fighter.up());
            card.css('transform', 'rotate(' + rotate.rotate + 'deg)');

            this.angle += this.separation_angle;
            ++i;
        }

        if (this.reverse_stacking) {
            this.z_fighter.up(this.card_container.getCards().length);
        }

        if (this.offset_zero_out) {
            this.calculateZeroOut();
            let i = 0;
            for (const card of this.card_container.getCards()) {
                card.css('left', this.card_positions[i].x + 'px');
                card.css('top', this.card_positions[i].y + 'px');
                ++i;
            }
        }

        if (this.add_dropper) {
            this.addDroppableArea();
        }
    }

    calculateZeroOut() {
        let x = 0;
        let y = 0;

        for (const card of this.card_positions) {
            x += card.x;
            y += card.y;
        }

        x /= this.card_positions.length;
        y /= this.card_positions.length;

        for (const [i, card] of this.card_positions.entries()) {
            this.card_positions[i].x -= x + this.offset_x;
            this.card_positions[i].y -= y + this.offset_y;
        }

        this.zero_out_x_avg = x + this.offset_x;
        this.zero_out_y_avg = y + this.offset_y;
    }

    getCoordinates(angle, add_deviation, i = null) {
        let x = (this.radius * Math.cos(MathHelper.degreesToRadians(angle))) + this.center_x;
        let y = (this.radius * Math.sin(MathHelper.degreesToRadians(angle))) + this.center_y;

        let div_x, div_y;
        let offset_x = 0;
        let offset_y = 0;

        if (add_deviation) {
            if (null !== i && i in this.card_offsets) {
                offset_x = this.card_offsets[i].offset_x;
                offset_y = this.card_offsets[i].offset_y;
            } else {
                offset_x = Math.random();
                offset_y = Math.random();
            }

            let div = this.card_width * this.random_deviation;
            div_x = Math.floor(offset_x * (2 * div)) - div;
            div_y = Math.floor(offset_y * (2 * div)) - div;

            x += div_x;
            y += div_y;
        }

        return {x: x, y: y, offset_x: offset_x, offset_y: offset_y};
    }

    getRotation(x, y, add_deviation, i = null) {
        let rotate = MathHelper.radiansToDegrees(Math.atan2(y - this.center_y, x - this.center_x)) + 90;
        let div_rotate;

        let offset_angle = 0;

        if (add_deviation) {
            if (null !== i && i in this.card_offsets) {
                offset_angle = this.card_offsets[i].offset_angle;
            } else {
                offset_angle = Math.random();
            }

            let div_angle = (this.card_width * this.random_deviation) * 8;
            div_rotate = (Math.floor(offset_angle * (2 * div_angle + 1)) - div_angle) / 10;
            rotate += div_rotate;
        }

        return  { rotate: rotate, offset_angle: offset_angle };
    }

    addDroppableArea() {
        let container = this.card_container.getContainer();
        this.z_fighter.up(this.card_count);

        this.addLeadingCardDropper(container);

        let i = 0;

        for (const card of this.card_container.getCards()) {
            const card_position = this.card_positions[i];
            this.addDropper(container, card, i + 1, card_position.x, card_position.y, card_position.rotate, this.z_fighter.current(), i === this.card_count - 1);
            this.z_fighter.up();
            ++i;
        }
    }

    addLeadingCardDropper(container) {
        this.angle = (-90 + this.offset_angle) - (((this.separation_angle * this.card_count) / 2) - (this.separation_angle / 2));
        this.angle -= this.separation_angle;
        let coords = this.getCoordinates(this.angle, false);
        let rotate = this.getRotation(coords.x, coords.y, false);
        this.addDropper(container, null, 0, coords.x - this.zero_out_x_avg, coords.y - this.zero_out_y_avg, rotate.rotate, this.z_fighter.current(), false);
    }

    addDropper(container, card, id, x, y, rotate, zindex, is_last) {
        let dropper = container.find(`.card-hand-dropper[data-dropper-id='${id}']`);
        let indicator = container.find(`.card-hand-dropper-indicator[data-dropper-id='${id}']`);
        let is_new = false;

        if (0 === dropper.length) {
            is_new = true;
            dropper = $(`<div class="card-hand-dropper" data-dropper-id="${id}"></div>`);
            indicator = $(`<div class="card-hand-dropper-indicator" data-dropper-id="${id}"></div>`);
        }

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

        if (is_new) {
            dropper.append(indicator);
            container.append(dropper);
        }
    }
}