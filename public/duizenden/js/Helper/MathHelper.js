class MathHelper {
    static degreesToRadians(degrees)
    {
      return degrees * (Math.PI / 180);
    }

    static radiansToDegrees(radians)
    {
      return radians * (180 / Math.PI);
    }

    static getAngle(p1, p2, in_rads = false) {
        const rads = Math.atan2(p2.y - p1.y, p2.x - p1.x);

        return in_rads ? rads : this.toDegrees(rads);
    }

    static toDegrees(rads) {
        return rads * (180 / Math.PI);
    }
}