class ZFighter {
    constructor(step) {
        this.step = step;
        this.z = 0;
    }

    up() {
        return this.z += this.step;
    }

    down() {
        return this.z -= this.step;
    }
}