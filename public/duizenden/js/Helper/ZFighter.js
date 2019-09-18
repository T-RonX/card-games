class ZFighter {
    constructor(step) {
        this.step = step;
        this.z = 0;
    }

    current() {
        return this.z;
    }

    up(step = null) {
        if (null === step) {
            step += this.step;
        }

        return this.z += step;
    }

    down(step = null) {
        if (null === step) {
            step += this.step;
        }

        return this.z -= step;
    }
}