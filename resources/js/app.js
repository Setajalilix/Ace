import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

async function acePost(url, { method = 'POST', body = null, form = null } = {}) {
    const headers = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
    };

    let payload = null;
    let verb = method;

    if (form) {
        payload = new FormData(form);
        if (method === 'PUT' || method === 'DELETE') {
            payload.append('_method', method);
            verb = 'POST';
        }
    } else if (body !== null) {
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
    }

    const response = await fetch(url, { method: verb, headers, body: payload });

    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        const firstFieldError = error.errors ? Object.values(error.errors).flat()[0] : null;
        throw new Error(firstFieldError ?? error.message ?? 'Something went wrong');
    }

    return response.json();
}

function aceToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('ace:toast', { detail: { message, type } }));
}

window.acePost = acePost;
window.aceToast = aceToast;

function loadTimerState(key, fallbackSeconds = 0) {
    try {
        const raw = localStorage.getItem(key);
        if (!raw) {
            return { seconds: fallbackSeconds, running: false };
        }
        const data = JSON.parse(raw);
        let seconds = data.seconds ?? fallbackSeconds;
        if (data.running && data.lastTick) {
            seconds += Math.floor((Date.now() - data.lastTick) / 1000);
        }

        return { seconds, running: Boolean(data.running) };
    } catch {
        return { seconds: fallbackSeconds, running: false };
    }
}

function saveTimerState(key, seconds, running) {
    localStorage.setItem(key, JSON.stringify({ seconds, running, lastTick: Date.now() }));
}

function formatTimer(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');

    return h > 0 ? `${h}:${m}:${s}` : `${m}:${s}`;
}

function setChipValue(name, value, form = null) {
    const v = String(value ?? '');
    if (form) {
        form.querySelectorAll(`[data-chip-field="${name}"]`).forEach((wrapper) => {
            const data = Alpine.$data(wrapper);
            if (data && 'selected' in data) {
                data.selected = v;
            }
        });
        return;
    }
    window.dispatchEvent(new CustomEvent('ace:chip-set', { detail: { name, value: v } }));
}

function localToday() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

async function fetchJalaliParts(gregorian) {
    if (!gregorian) return null;
    const response = await fetch(`/calendar/date-parts?date=${encodeURIComponent(gregorian)}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!response.ok) return null;
    return response.json();
}

function prepareFormDates(form) {
    form.querySelectorAll('[data-date-field]').forEach((wrapper) => {
        const data = Alpine.$data(wrapper);
        if (data?.prepareSubmit) {
            data.prepareSubmit();
        }
    });
}

function resetFormDates(form) {
    form.querySelectorAll('[data-date-field]').forEach((wrapper) => {
        const data = Alpine.$data(wrapper);
        if (data?.resetFields) {
            data.resetFields();
        }
    });
}

function removeHabitTodayRowById(id) {
    const row = document.querySelector(`.habit-today-row[data-habit-id="${id}"]`);
    if (!row) return;

    row.style.opacity = '0';
    row.style.transform = 'translateX(10px)';
    row.style.maxHeight = `${row.offsetHeight}px`;
    row.style.overflow = 'hidden';
    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease, max-height 0.35s ease, margin 0.35s ease';

    setTimeout(() => {
        row.style.maxHeight = '0';
        row.style.marginBottom = '0';
    }, 50);

    setTimeout(() => {
        row.remove();
        const list = document.querySelector('[data-today-habits]');
        if (list && !list.querySelector('.habit-today-row') && !list.querySelector('.habit-today-empty')) {
            const empty = document.createElement('p');
            empty.className = 'text-sm text-[#A8958B] habit-today-empty';
            empty.textContent = 'No habits due.';
            list.appendChild(empty);
        }
    }, 380);
}

function notifyHabitDone(id) {
    window.dispatchEvent(new CustomEvent('ace:habit-done', { detail: { id } }));
}

function snapshotForm(form) {
    if (!form) return '';
    const parts = [];
    new FormData(form).forEach((value, key) => {
        if (key === '_token' || key === '_method') return;
        parts.push(`${key}=${value}`);
    });
    return parts.sort().join('&');
}

Alpine.data('dateInput', (prefix) => ({
    tab: 'jalali',
    prefix,

    async setGregorian(value) {
        if (!value) return;
        if (this.$refs.gregorian) {
            this.$refs.gregorian.value = value;
        }
        const parts = await fetchJalaliParts(value);
        if (!parts) return;
        if (this.$refs.jDay) this.$refs.jDay.value = String(parts.day);
        if (this.$refs.jMonth) this.$refs.jMonth.value = String(parts.month);
        if (this.$refs.jYear) this.$refs.jYear.value = String(parts.year);
        if (this.$refs.jHidden) this.$refs.jHidden.value = parts.jalali;
    },

    prepareSubmit() {
        const g = this.$refs.gregorian;
        const d = this.$refs.jDay;
        const m = this.$refs.jMonth;
        const y = this.$refs.jYear;
        const h = this.$refs.jHidden;

        if (this.tab === 'jalali') {
            if (d?.value && m?.value && y?.value && h) {
                h.value = `${y.value}/${m.value}/${d.value}`;
            }
            if (g) g.disabled = true;
        } else {
            if (h) h.value = '';
            [d, m, y].forEach((el) => { if (el) el.disabled = true; });
        }
    },

    resetFields() {
        const g = this.$refs.gregorian;
        const d = this.$refs.jDay;
        const m = this.$refs.jMonth;
        const y = this.$refs.jYear;
        if (g) g.disabled = false;
        [d, m, y].forEach((el) => { if (el) el.disabled = false; });
    },
}));

function fillHabitForm(form, habit) {
    if (!form || !habit) return;
    const set = (name, value) => {
        const el = form.elements.namedItem(name);
        if (el && 'value' in el) el.value = value ?? '';
    };
    set('title', habit.title);
    set('type', habit.type);
    set('color', habit.color ?? '#7BAE7F');
    set('repeat_every', habit.repeat_every ?? 1);
    set('target_minutes', habit.target_minutes ?? 5);
    set('target_count', habit.target_count ?? 5);
    set('daily_increment', habit.daily_increment ?? 0);
    setChipValue('life_area_id', habit.life_area_id ?? '', form);
    window.dispatchEvent(new CustomEvent('ace:habit-fill', { detail: habit }));
    window.dispatchEvent(new CustomEvent('ace:date-set', {
        detail: { name: 'start_date', value: habit.start_date ?? localToday() },
    }));
}

function fillGoalForm(form, goal) {
    if (!form || !goal) return;
    const set = (name, value) => {
        const el = form.elements.namedItem(name);
        if (el && 'value' in el) el.value = value ?? '';
    };
    set('title', goal.title);
    set('why', goal.why ?? '');
    set('success_criteria', goal.success_criteria ?? '');
    set('type', goal.type ?? 'annual');
    set('progress', goal.progress ?? 0);
    setChipValue('life_area_id', goal.life_area_id ?? '', form);
    window.dispatchEvent(new CustomEvent('ace:date-set', {
        detail: { name: 'target_date', value: goal.target_date ?? '' },
    }));
}

function fillEventForm(form, event) {
    if (!form || !event) return;
    const set = (name, value) => {
        const el = form.elements.namedItem(name);
        if (el && 'value' in el) el.value = value ?? '';
    };
    set('title', event.title);
    set('location', event.location ?? '');
    set('start_time', event.start_time ?? '');
    set('end_time', event.end_time ?? '');
    setChipValue('life_area_id', event.life_area_id ?? '', form);
    setChipValue('status', event.status ?? 'scheduled', form);
    window.dispatchEvent(new CustomEvent('ace:date-set', {
        detail: { name: 'start_date', value: event.start_date ?? localToday() },
    }));
    if (event.end_date) {
        window.dispatchEvent(new CustomEvent('ace:date-set', {
            detail: { name: 'end_date', value: event.end_date },
        }));
    }
    window.dispatchEvent(new CustomEvent('ace:event-fill', { detail: event }));
}

function fillTaskForm(form, task) {
    if (!form || !task) return;

    const set = (name, value) => {
        const el = form.elements.namedItem(name);
        if (el && 'value' in el) {
            el.value = value ?? '';
        }
    };

    set('title', task.title);
    set('description', task.description ?? '');
    set('priority', task.priority);
    set('status', task.status ?? 'pending');
    set('life_area_id', task.life_area_id ?? '');
    set('goal_id', task.goal_id ?? '');
    set('estimated_minutes', task.estimated_minutes ?? 30);
    set('scheduled_time', task.scheduled_time ?? '');

    setChipValue('priority', task.priority, form);
    setChipValue('life_area_id', task.life_area_id ?? '', form);
    setChipValue('goal_id', task.goal_id ?? '', form);
    setChipValue('status', task.status ?? 'pending', form);

    window.dispatchEvent(new CustomEvent('ace:date-set', {
        detail: { name: 'due_date', value: task.due_date ?? '' },
    }));
}

Alpine.data('appShell', () => ({
    open: false,
    taskOpen: false,
    taskEditId: null,
    taskSaving: false,
    habitOpen: false,
    habitEditId: null,
    habitSaving: false,
    goalOpen: false,
    goalEditId: null,
    goalSaving: false,
    eventOpen: false,
    eventEditId: null,
    eventSaving: false,
    fabMenu: false,
    body: '',
    toasts: [],
    modalSnapshots: {},
    captureSnapshot: '',

    init() {
        window.addEventListener('ace:toast', (e) => this.pushToast(e.detail.message, e.detail.type));
        window.addEventListener('ace:open-task-edit', (e) => this.openTaskEdit(e.detail));
        window.addEventListener('ace:open-habit-edit', (e) => this.openHabitEdit(e.detail));
        window.addEventListener('ace:open-habit-create', () => this.openHabitCreate());
        window.addEventListener('ace:open-goal-edit', (e) => this.openGoalEdit(e.detail));
        window.addEventListener('ace:open-goal-create', () => this.openGoalCreate());
        window.addEventListener('ace:open-event-edit', (e) => this.openEventEdit(e.detail));
        window.addEventListener('ace:open-event-create', () => this.openEventCreate());
        window.addEventListener('ace:habit-done', (e) => removeHabitTodayRowById(e.detail.id));
        window.addEventListener('ace:delete-habit', (e) => this.deleteHabit(e.detail.id));
    },

    pushToast(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        }, 3200);
    },

    captureModalSnapshot(key, form) {
        this.modalSnapshots[key] = snapshotForm(form);
    },

    formIsDirty(key, form) {
        if (!form) return false;
        return this.modalSnapshots[key] !== snapshotForm(form);
    },

    confirmCloseModal(key, form) {
        if (this.formIsDirty(key, form)) {
            return confirm('Discard unsaved changes and close?');
        }
        return true;
    },

    requestCloseCapture() {
        if (this.body.trim() !== this.captureSnapshot && !confirm('Discard unsaved changes and close?')) return;
        this.open = false;
        this.body = '';
        this.captureSnapshot = '';
    },

    openCapture() {
        this.body = '';
        this.captureSnapshot = '';
        this.open = true;
    },

    requestCloseTask() {
        if (!this.confirmCloseModal('task', this.$refs.taskForm)) return;
        this.taskOpen = false;
        this.taskEditId = null;
    },

    requestCloseHabit() {
        if (!this.confirmCloseModal('habit', this.$refs.habitForm)) return;
        this.habitOpen = false;
        this.habitEditId = null;
    },

    requestCloseGoal() {
        if (!this.confirmCloseModal('goal', this.$refs.goalForm)) return;
        this.goalOpen = false;
        this.goalEditId = null;
    },

    requestCloseEvent() {
        if (!this.confirmCloseModal('event', this.$refs.eventForm)) return;
        this.eventOpen = false;
        this.eventEditId = null;
    },

    openTaskCreate() {
        this.taskEditId = null;
        this.taskOpen = true;
        this.$nextTick(() => {
            const form = this.$refs.taskForm;
            form?.reset();
            setChipValue('priority', '2', form);
            setChipValue('life_area_id', '', form);
            setChipValue('goal_id', '', form);
            window.dispatchEvent(new CustomEvent('ace:date-set', {
                detail: { name: 'due_date', value: localToday() },
            }));
            this.$nextTick(() => this.captureModalSnapshot('task', form));
        });
    },

    openTaskEdit(task) {
        this.taskEditId = task.id;
        this.taskOpen = true;
        this.$nextTick(() => {
            fillTaskForm(this.$refs.taskForm, task);
            this.$nextTick(() => this.captureModalSnapshot('task', this.$refs.taskForm));
        });
    },

    openHabitCreate() {
        this.habitEditId = null;
        this.habitOpen = true;
        this.$nextTick(() => {
            const form = this.$refs.habitForm;
            form?.reset();
            setChipValue('life_area_id', '', form);
            window.dispatchEvent(new CustomEvent('ace:habit-fill', { detail: { type: 'checkbox' } }));
            window.dispatchEvent(new CustomEvent('ace:date-set', {
                detail: { name: 'start_date', value: localToday() },
            }));
            this.$nextTick(() => this.captureModalSnapshot('habit', form));
        });
    },

    openHabitEdit(habit) {
        this.habitEditId = habit.id;
        this.habitOpen = true;
        this.$nextTick(() => {
            fillHabitForm(this.$refs.habitForm, habit);
            this.$nextTick(() => this.captureModalSnapshot('habit', this.$refs.habitForm));
        });
    },

    openGoalCreate() {
        this.goalEditId = null;
        this.goalOpen = true;
        this.$nextTick(() => {
            const form = this.$refs.goalForm;
            form?.reset();
            setChipValue('life_area_id', '', form);
            this.$nextTick(() => this.captureModalSnapshot('goal', form));
        });
    },

    openGoalEdit(goal) {
        this.goalEditId = goal.id;
        this.goalOpen = true;
        this.$nextTick(() => {
            fillGoalForm(this.$refs.goalForm, goal);
            this.$nextTick(() => this.captureModalSnapshot('goal', this.$refs.goalForm));
        });
    },

    openEventCreate() {
        this.eventEditId = null;
        this.eventOpen = true;
        this.$nextTick(() => {
            const form = this.$refs.eventForm;
            form?.reset();
            setChipValue('life_area_id', '', form);
            setChipValue('status', 'scheduled', form);
            window.dispatchEvent(new CustomEvent('ace:event-fill', { detail: { recurrence: 'none' } }));
            window.dispatchEvent(new CustomEvent('ace:date-set', {
                detail: { name: 'start_date', value: localToday() },
            }));
            window.dispatchEvent(new CustomEvent('ace:date-set', {
                detail: { name: 'end_date', value: localToday() },
            }));
            this.$nextTick(() => this.captureModalSnapshot('event', form));
        });
    },

    openEventEdit(event) {
        this.eventEditId = event.id;
        this.eventOpen = true;
        this.$nextTick(() => {
            fillEventForm(this.$refs.eventForm, event);
            this.$nextTick(() => this.captureModalSnapshot('event', this.$refs.eventForm));
        });
    },

    async submitCapture() {
        if (!this.body.trim()) return;
        await acePost('/inbox/quick', { body: { body: this.body } });
        this.body = '';
        this.open = false;
        this.fabMenu = false;
        aceToast('Captured to inbox');
    },

    async submitTask(event) {
        event.preventDefault();
        this.taskSaving = true;
        const form = event.target;
        prepareFormDates(form);
        const url = this.taskEditId ? `/tasks/${this.taskEditId}` : '/tasks';
        const method = this.taskEditId ? 'PUT' : 'POST';

        try {
            const data = await acePost(url, { method, form });
            this.taskOpen = false;
            this.taskEditId = null;
            aceToast(data.message ?? (method === 'PUT' ? 'Task updated' : 'Task created'));
            window.dispatchEvent(new CustomEvent('ace:task-saved', { detail: data.task ?? null }));
            if (!document.querySelector('[data-kanban-board]')) {
                window.location.reload();
            }
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            resetFormDates(form);
            this.taskSaving = false;
        }
    },

    async submitHabit(event) {
        event.preventDefault();
        this.habitSaving = true;
        const form = event.target;
        prepareFormDates(form);
        const url = this.habitEditId ? `/habits/${this.habitEditId}` : '/habits';
        const method = this.habitEditId ? 'PUT' : 'POST';

        try {
            const data = await acePost(url, { method, form });
            this.habitOpen = false;
            this.habitEditId = null;
            aceToast(data.message ?? 'Habit saved');
            window.location.reload();
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            resetFormDates(form);
            this.habitSaving = false;
        }
    },

    async deleteHabit(id = null) {
        const targetId = id ?? this.habitEditId;
        if (!targetId) return;
        if (!confirm('Delete this habit permanently? All history will be lost.')) return;

        try {
            await acePost(`/habits/${targetId}`, { method: 'DELETE' });
            this.habitOpen = false;
            this.habitEditId = null;
            aceToast('Habit deleted');
            if (window.location.pathname.match(/\/habits\/\d+/)) {
                window.location.href = '/habits';
            } else {
                window.location.reload();
            }
        } catch (err) {
            aceToast(err.message, 'error');
        }
    },

    async submitGoal(event) {
        event.preventDefault();
        this.goalSaving = true;
        const form = event.target;
        prepareFormDates(form);
        const url = this.goalEditId ? `/goals/${this.goalEditId}` : '/goals';
        const method = this.goalEditId ? 'PUT' : 'POST';

        try {
            const data = await acePost(url, { method, form });
            this.goalOpen = false;
            this.goalEditId = null;
            aceToast(data.message ?? 'Goal saved');
            window.location.reload();
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            resetFormDates(form);
            this.goalSaving = false;
        }
    },

    async submitEvent(event) {
        event.preventDefault();
        this.eventSaving = true;
        const form = event.target;
        prepareFormDates(form);
        const url = this.eventEditId ? `/events/${this.eventEditId}` : '/events';
        const method = this.eventEditId ? 'PUT' : 'POST';

        try {
            const data = await acePost(url, { method, form });
            this.eventOpen = false;
            this.eventEditId = null;
            aceToast(data.message ?? 'Event saved');
            window.location.reload();
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            resetFormDates(form);
            this.eventSaving = false;
        }
    },
}));

Alpine.data('taskRow', (task) => ({
    task,
    removing: false,
    completing: false,
    completed: task.completed ?? false,
    showBlock: false,

    async complete() {
        if (this.completed || this.completing) return;
        this.completing = true;
        try {
            await acePost(`/tasks/${this.task.id}/complete`, {});
            this.completed = true;
            aceToast('Nice — task done!');
            window.dispatchEvent(new CustomEvent('ace:task-completed', { detail: { id: this.task.id } }));
            setTimeout(() => {
                this.removing = true;
                setTimeout(() => this.$el.remove(), 350);
            }, 400);
        } catch (err) {
            aceToast(err.message, 'error');
            this.completing = false;
        }
    },

    async start() {
        try {
            await acePost(`/tasks/${this.task.id}/start`, {});
            aceToast('Task started');
            window.dispatchEvent(new CustomEvent('ace:task-started', { detail: { id: this.task.id } }));
        } catch (err) {
            aceToast(err.message, 'error');
        }
    },

    openEdit() {
        window.dispatchEvent(new CustomEvent('ace:open-task-edit', { detail: this.task }));
    },
}));

Alpine.data('habitInteract', (config) => ({
    ...config,
    completing: false,
    saving: false,

    async toggleCheckbox() {
        if (this.completing) return;
        this.completing = true;
        try {
            const data = await acePost(`/habits/${this.id}/toggle`, {});
            this.completed = data.completed;
            aceToast(data.completed ? 'Habit done!' : 'Habit unchecked');
            if (data.completed) {
                notifyHabitDone(this.id);
            }
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            this.completing = false;
        }
    },

    async incrementCounter() {
        try {
            const data = await acePost(`/habits/${this.id}/counter/increment`, {});
            this.count = data.count;
            this.completed = data.completed;
            this.pct = data.pct;
            if (data.completed) {
                aceToast('Target reached!');
                notifyHabitDone(this.id);
            }
        } catch (err) {
            aceToast(err.message, 'error');
        }
    },

    async setCounter(event) {
        event.preventDefault();
        if (this.saving) return;
        this.saving = true;
        const form = event.target;
        try {
            const data = await acePost(`/habits/${this.id}/counter`, { form });
            this.count = data.count;
            this.completed = data.completed;
            this.pct = data.pct;
            aceToast(data.completed ? 'Target reached!' : 'Count saved');
            if (data.completed) {
                notifyHabitDone(this.id);
            }
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            this.saving = false;
        }
    },
}));

Alpine.data('focusTimer', (initialSeconds = 0, storageKey = 'ace_focus_timer') => ({
    seconds: 0,
    running: false,
    interval: null,
    storageKey,
    init() {
        const state = loadTimerState(this.storageKey, initialSeconds);
        this.seconds = state.seconds;
        if (state.running) {
            this.startInterval(false);
        }
    },
    get formatted() {
        return formatTimer(this.seconds);
    },
    get ringPct() {
        const cycle = 25 * 60;
        return ((this.seconds % cycle) / cycle) * 100;
    },
    startInterval(persistRunning = true) {
        this.running = true;
        if (persistRunning) {
            saveTimerState(this.storageKey, this.seconds, true);
        }
        this.interval = setInterval(() => {
            this.seconds++;
            saveTimerState(this.storageKey, this.seconds, true);
        }, 1000);
    },
    toggle() {
        if (this.running) {
            clearInterval(this.interval);
            this.running = false;
            saveTimerState(this.storageKey, this.seconds, false);
        } else {
            this.startInterval();
        }
    },
    reset() {
        clearInterval(this.interval);
        this.running = false;
        this.seconds = 0;
        localStorage.removeItem(this.storageKey);
    },
}));

Alpine.data('habitTimer', (habitId, initialMinutes = 0) => ({
    seconds: 0,
    running: false,
    interval: null,
    storageKey: `ace_habit_timer_${habitId}`,
    loggedMinutes: initialMinutes,
    targetMinutes: 0,
    saving: false,
    completedFlag: false,
    init() {
        const state = loadTimerState(this.storageKey, initialMinutes * 60);
        this.seconds = state.seconds;
        if (state.running) {
            this.startInterval(false);
        }
    },
    get formatted() {
        return formatTimer(this.seconds);
    },
    get displayTime() {
        const total = this.loggedMinutes * 60 + this.seconds;
        return formatTimer(total);
    },
    get loggedDisplay() {
        const total = this.loggedMinutes * 60 + this.seconds;
        const m = Math.floor(total / 60);
        const s = total % 60;
        if (m === 0) return `${s}s`;
        if (s === 0) return `${m} min`;
        return `${m}m ${s}s`;
    },
    get pct() {
        if (!this.targetMinutes) return 0;
        const totalSeconds = this.loggedMinutes * 60 + this.seconds;
        return Math.min(100, (totalSeconds / (this.targetMinutes * 60)) * 100);
    },
    startInterval(persistRunning = true) {
        this.running = true;
        if (persistRunning) {
            saveTimerState(this.storageKey, this.seconds, true);
        }
        this.interval = setInterval(() => {
            this.seconds++;
            saveTimerState(this.storageKey, this.seconds, true);
            if (this.seconds > 0 && this.seconds % 60 === 0) {
                this.persistProgress(true);
            }
            if (this.pct >= 100 && !this.completedFlag) {
                this.completedFlag = true;
                clearInterval(this.interval);
                this.running = false;
                this.persistProgress(false);
            }
        }, 1000);
    },
    toggle() {
        if (this.running) {
            clearInterval(this.interval);
            this.running = false;
            saveTimerState(this.storageKey, this.seconds, false);
            this.persistProgress(true);
        } else {
            this.startInterval();
        }
    },
    async persistProgress(silent = false) {
        const addedMinutes = Math.floor(this.seconds / 60);
        if (addedMinutes < 1) return;
        if (this.saving) return;
        this.saving = true;
        const minutes = this.loggedMinutes + addedMinutes;
        try {
            const data = await acePost(`/habits/${habitId}/timer`, { body: { spent_minutes: minutes } });
            this.loggedMinutes = data.spent_minutes ?? minutes;
            this.seconds = this.seconds % 60;
            saveTimerState(this.storageKey, this.seconds, this.running);
            if (!silent) {
                aceToast(data.completed ? 'Habit target reached!' : 'Progress saved');
            } else if (data.completed) {
                aceToast('Habit target reached!');
            }
            if (data.completed) {
                notifyHabitDone(habitId);
            }
        } catch (err) {
            if (!silent) aceToast(err.message, 'error');
        } finally {
            this.saving = false;
        }
    },
}));

Alpine.data('journalForm', (type, date) => ({
    type,
    date,
    saving: false,
    saved: false,

    async submit(event) {
        event.preventDefault();
        if (this.saving) return;
        this.saving = true;
        try {
            await acePost('/journal', { form: event.target });
            this.saved = true;
            aceToast('Journal saved');
            setTimeout(() => { this.saved = false; }, 2000);
        } catch (err) {
            aceToast(err.message, 'error');
        } finally {
            this.saving = false;
        }
    },
}));

Alpine.start();

document.addEventListener('submit', (event) => {
    if (event.target?.querySelector?.('[data-date-field]')) {
        prepareFormDates(event.target);
    }
}, true);
