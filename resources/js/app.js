import './bootstrap';
window.timer = function (habitId, target) {

    return {
        seconds: 0,
        interval: null,
        target: target,

        get minutes() {
            return Math.floor(this.seconds / 60)
        },

        get progress() {
            return Math.min(
                (this.minutes / this.target) * 100,
                100
            )
        },

        start() {
            if (this.interval) return

            this.interval = setInterval(() => {
                this.seconds++
            }, 1000)
        },

        stop() {
            clearInterval(this.interval)

            fetch(`/habits/${habitId}/timer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                },
                body: JSON.stringify({
                    minutes: this.minutes,
                })
            })
        }
    }
}
