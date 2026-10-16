@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card {
        background: #fff;
        border: 1px solid var(--mc-line);
        border-radius: 1.25rem;
    }

    .mc-muted {
        color: var(--mc-muted);
    }

    .mc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem;
        padding: .6rem 1.25rem;
        border-radius: .875rem;
        font-size: .875rem;
        font-weight: 600;
        transition: background .2s, color .2s;
    }

    .mc-btn-primary {
        background: var(--mc-ink);
        color: #fff;
    }

    .mc-btn-primary:hover {
        background: var(--mc-primary);
    }

    .mc-btn-danger {
        border: 1px solid #EBC9C5;
        color: #B4443C;
        background: #fff;
    }

    .mc-btn-danger:hover {
        background: #FBF1F0;
    }

    .mc-btn:focus-visible {
        outline: 3px solid var(--mc-sun);
        outline-offset: 2px;
    }

    .mc-field-label {
        font-size: .8125rem;
        font-weight: 600;
        color: var(--mc-muted);
    }

    /* ---------- FullCalendar ---------- */
    #calendar {
        --fc-border-color: var(--mc-line);
        --fc-page-bg-color: #fff;
        --fc-neutral-bg-color: #F5F9F6;
        --fc-today-bg-color: #FDF3DE;
        --fc-button-bg-color: var(--mc-ink);
        --fc-button-border-color: var(--mc-ink);
        --fc-button-hover-bg-color: var(--mc-primary);
        --fc-button-hover-border-color: var(--mc-primary);
        --fc-button-active-bg-color: var(--mc-primary);
        --fc-button-active-border-color: var(--mc-primary);
        --fc-event-bg-color: var(--mc-primary);
        --fc-event-border-color: var(--mc-primary);
        font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    #calendar .fc-toolbar {
        flex-wrap: wrap;
        gap: .75rem;
    }

    #calendar .fc-toolbar-title {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 1.5rem;
        font-weight: 600;
    }

    #calendar .fc-button {
        border-radius: .75rem;
        font-size: .8125rem;
        font-weight: 600;
        text-transform: none;
        padding: .5rem .8rem;
    }

    #calendar .fc-button:focus {
        box-shadow: 0 0 0 3px var(--mc-sun);
    }

    #calendar .fc-col-header-cell-cushion,
    #calendar .fc-daygrid-day-number {
        color: var(--mc-ink);
        font-size: .8125rem;
    }

    #calendar .fc-event {
        border-radius: .5rem;
        cursor: pointer;
    }

    @media (max-width: 640px) {
        #calendar .fc-toolbar {
            justify-content: center;
        }

        #calendar .fc-toolbar-title {
            font-size: 1.15rem;
            order: -1;
            width: 100%;
            text-align: center;
        }

        #calendar .fc-button {
            padding: .45rem .65rem;
        }
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold">
                Calendar
            </h1>

            <p class="mt-1 text-sm mc-muted">
                Atur jadwal dan aktivitasmu agar harimu lebih terarah.
            </p>
        </div>

        <a
            href="{{ route('calendar.create') }}"
            class="mc-btn mc-btn-primary gap-2">

            <span class="text-lg leading-none">+</span>
            Tambah Jadwal

        </a>

    </div>


    {{-- Calendar --}}
    <div class="mc-card">

        <div class="p-3 sm:p-6">

            <div id="calendar"></div>

        </div>

    </div>

</div>


{{-- ========================= --}}
{{-- EVENT DETAIL MODAL --}}
{{-- ========================= --}}

<div
    id="event-modal"
    class="hidden fixed inset-0 z-[200]
           flex items-center justify-center
           px-4"
    style="background: rgba(22,48,43,.55);">

    <div
        class="w-full max-w-md
               rounded-3xl bg-white
               shadow-xl
               max-h-[90vh] overflow-y-auto">

        {{-- Modal Header --}}
        <div
            class="flex items-center justify-between gap-3
                   px-6 py-5
                   border-b"
            style="border-color: var(--mc-line);">

            <h2
                id="event-title"
                class="text-xl font-semibold break-words min-w-0">
            </h2>

            <button
                id="close-event-modal"
                type="button"
                aria-label="Tutup"
                class="w-11 h-11 shrink-0 rounded-full
                       mc-muted
                       hover:bg-[var(--mc-soft)]
                       transition">

                ✕

            </button>

        </div>


        {{-- Modal Content --}}
        <div class="p-6 space-y-5">

            {{-- Type --}}
            <div>

                <p class="mc-field-label">
                    Tipe
                </p>

                <p
                    id="event-type"
                    class="mt-1 text-sm
                           font-semibold">
                </p>

            </div>


            {{-- Time --}}
            <div>

                <p class="mc-field-label">
                    Waktu
                </p>

                <p
                    id="event-time"
                    class="mt-1 text-sm">
                </p>

            </div>


            {{-- Description --}}
            <div>

                <p class="mc-field-label">
                    Deskripsi
                </p>

                <p
                    id="event-description"
                    class="mt-1 text-sm break-words">
                </p>

            </div>


            {{-- Fixed --}}
            <div
                id="event-fixed"
                class="rounded-xl
                       px-4 py-3
                       text-sm"
                style="background: var(--mc-soft); color: var(--mc-primary);">
            </div>


            {{-- Buttons --}}
            <div class="flex gap-3 pt-2">

                {{-- Edit --}}
                <a
                    id="edit-event"
                    href="#"
                    class="mc-btn mc-btn-primary flex-1 text-center">

                    Edit Jadwal

                </a>


                {{-- Delete --}}
                <form
                    id="delete-event-form"
                    method="POST"
                    class="flex-1"
                    onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="mc-btn mc-btn-danger w-full">

                        Hapus

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- ========================= --}}
{{-- DATA LARAVEL --}}
{{-- ========================= --}}

<script>
    window.calendarEvents = @json($calendarEvents);

    /*
     * Base URL untuk delete.
     *
     * Edit sekarang dibuat langsung menggunakan
     * ID Schedule yang sudah dibersihkan dari
     * prefix "schedule-".
     */

    window.scheduleDeleteUrl =
        "{{ route('calendar.destroy', ['schedule' => '__ID__']) }}";
</script>


{{-- ========================= --}}
{{-- EVENT MODAL SCRIPT --}}
{{-- ========================= --}}

<script>

    const eventModal =
        document.getElementById('event-modal');


    const closeEventModal =
        document.getElementById('close-event-modal');


    const editEvent =
        document.getElementById('edit-event');


    const deleteEventForm =
        document.getElementById('delete-event-form');


    const eventTitle =
        document.getElementById('event-title');


    const eventType =
        document.getElementById('event-type');


    const eventTime =
        document.getElementById('event-time');


    const eventDescription =
        document.getElementById('event-description');


    const eventFixed =
        document.getElementById('event-fixed');


    // =================================================
    // FORMAT TIPE
    // =================================================

    function formatEventType(type) {

        const types = {

            academic: 'Akademik',

            organization: 'Organisasi',

            personal: 'Pribadi',

            rest: 'Istirahat',

            other: 'Lainnya',

        };


        return types[type] ?? type;

    }


    // =================================================
    // FORMAT WAKTU
    // =================================================

    function formatEventTime(start, end) {

        const startDate =
            new Date(start);


        const endDate =
            new Date(end);


        const date =
            startDate.toLocaleDateString(
                'id-ID',
                {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }
            );


        const startTime =
            startDate.toLocaleTimeString(
                'id-ID',
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );


        const endTime =
            endDate.toLocaleTimeString(
                'id-ID',
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );


        return `${date} • ${startTime} - ${endTime}`;

    }


    // =================================================
    // BUKA MODAL EVENT
    // =================================================

    function openEventModal(event) {

        /*
         * Hanya Schedule yang boleh diedit/dihapus.
         *
         * Task tidak memiliki route edit Schedule.
         */

        const eventId =
            event.id;


        const isSchedule =
            typeof eventId === 'string'
            && eventId.startsWith('schedule-');


        // ---------------------------------------------
        // DATA MODAL
        // ---------------------------------------------

        eventTitle.textContent =
            event.title ?? '-';


        eventType.textContent =
            formatEventType(event.type);


        eventTime.textContent =
            formatEventTime(
                event.start,
                event.end
            );


        eventDescription.textContent =
            event.description
                ? event.description
                : 'Tidak ada deskripsi.';


        eventFixed.textContent =
            event.is_fixed
                ? '🔒 Jadwal tetap'
                : 'Jadwal fleksibel';


        // ---------------------------------------------
        // JIKA BUKAN SCHEDULE
        // ---------------------------------------------

        if (!isSchedule) {

            editEvent.classList.add('hidden');

            deleteEventForm.classList.add('hidden');

        } else {

            editEvent.classList.remove('hidden');

            deleteEventForm.classList.remove('hidden');


            /*
             * schedule-123
             *
             * menjadi:
             *
             * 123
             */

            const scheduleId =
                eventId.replace(
                    'schedule-',
                    ''
                );


            // -----------------------------------------
            // EDIT URL
            // -----------------------------------------

            editEvent.href =
                `/calendar/${scheduleId}/edit`;


            // -----------------------------------------
            // DELETE URL
            // -----------------------------------------

            deleteEventForm.action =
                window.scheduleDeleteUrl.replace(
                    '__ID__',
                    scheduleId
                );

        }


        // ---------------------------------------------
        // TAMPILKAN MODAL
        // ---------------------------------------------

        eventModal.classList.remove('hidden');

    }


    // =================================================
    // TUTUP MODAL
    // =================================================

    function closeModal() {

        eventModal.classList.add('hidden');

    }


    closeEventModal.addEventListener(
        'click',
        closeModal
    );


    // Klik background untuk menutup
    eventModal.addEventListener(
        'click',
        function(event) {

            if (event.target === eventModal) {

                closeModal();

            }

        }
    );


    // ESC untuk menutup
    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {

                closeModal();

            }

        }
    );


    // =================================================
    // FULLCALENDAR
    // =================================================

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const calendarEl =
                document.getElementById('calendar');


            if (!calendarEl) {

                return;

            }


            const calendar =
                new FullCalendar.Calendar(
                    calendarEl,
                    {

                        initialView: 'dayGridMonth',

                        height: 'auto',

                        locale: 'id',


                        headerToolbar: {

                            left: 'prev,next today',

                            center: 'title',

                            right:
                                'dayGridMonth,timeGridWeek,timeGridDay'

                        },


                        events:
                            window.calendarEvents,


                        eventClick:
                            function(info) {

                                info.jsEvent.preventDefault();

                                openEventModal(
                                    info.event
                                );

                            },

                    }
                );


            calendar.render();

        }
    );

</script>

@endsection