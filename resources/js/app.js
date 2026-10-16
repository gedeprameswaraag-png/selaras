import "./bootstrap";

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");

    if (!calendarEl) {
        return;
    }

    const events = (window.calendarEvents || []).map((event) => {
        let backgroundColor = "#64748b";
        let borderColor = "#64748b";

        // =========================
        // TASK
        // =========================

        if (event.event_type === "task") {
            if (event.priority === "high") {
                backgroundColor = "#ef4444";
                borderColor = "#ef4444";
            } else if (event.priority === "medium") {
                backgroundColor = "#f59e0b";
                borderColor = "#f59e0b";
            } else {
                backgroundColor = "#10b981";
                borderColor = "#10b981";
            }
        }

        // =========================
        // SCHEDULE
        // =========================
        else {
            switch (event.type) {
                case "academic":
                    backgroundColor = "#3b82f6";
                    borderColor = "#3b82f6";
                    break;

                case "organization":
                    backgroundColor = "#8b5cf6";
                    borderColor = "#8b5cf6";
                    break;

                case "personal":
                    backgroundColor = "#10b981";
                    borderColor = "#10b981";
                    break;

                case "rest":
                    backgroundColor = "#f59e0b";
                    borderColor = "#f59e0b";
                    break;

                case "other":
                    backgroundColor = "#64748b";
                    borderColor = "#64748b";
                    break;
            }
        }

        return {
            ...event,
            backgroundColor: backgroundColor,
            borderColor: borderColor,
        };
    });

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],

        initialView: "dayGridMonth",

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay",
        },

        buttonText: {
            today: "Hari ini",
            month: "Bulan",
            week: "Minggu",
            day: "Hari",
        },

        locale: "id",

        height: "auto",

        events: events,

        // Ketika event diklik
        eventClick: function (info) {
            const event = info.event;
            if (event.extendedProps.event_type === "task") {
                const priorityLabels = {
                    low: "Rendah",
                    medium: "Sedang",
                    high: "Tinggi",
                };

                const statusLabels = {
                    todo: "Belum dikerjakan",
                    in_progress: "Sedang dikerjakan",
                    completed: "Selesai",
                };

                document.getElementById("event-title").textContent =
                    event.title;

                document.getElementById("event-type").textContent = "Task";

                document.getElementById("event-time").textContent = event.start
                    ? event.start.toLocaleString("id-ID", {
                          dateStyle: "full",
                          timeStyle: "short",
                      })
                    : "-";

                document.getElementById("event-description").textContent =
                    event.extendedProps.description || "Tidak ada deskripsi.";

                document.getElementById("event-fixed").textContent =
                    `Prioritas: ${
                        priorityLabels[event.extendedProps.priority] || "-"
                    } • Status: ${
                        statusLabels[event.extendedProps.status] || "-"
                    } • Estimasi: ${
                        event.extendedProps.estimated_duration || 0
                    } menit`;

                document.getElementById("edit-event").href =
                    `/tasks/${event.id.replace("task-", "")}/edit`;

                document.getElementById("delete-event-form").action =
                    `/tasks/${event.id.replace("task-", "")}`;

                document
                    .getElementById("event-modal")
                    .classList.remove("hidden");

                return;
            }

            const typeLabels = {
                academic: "Akademik",
                organization: "Organisasi",
                personal: "Pribadi",
                rest: "Istirahat",
                other: "Lainnya",
            };

            const type = event.extendedProps.type;
            const description = event.extendedProps.description;
            const isFixed = event.extendedProps.is_fixed;

            const start = event.start
                ? event.start.toLocaleString("id-ID", {
                      dateStyle: "full",
                      timeStyle: "short",
                  })
                : "-";

            const end = event.end
                ? event.end.toLocaleTimeString("id-ID", {
                      hour: "2-digit",
                      minute: "2-digit",
                  })
                : "-";

            document.getElementById("event-title").textContent = event.title;

            document.getElementById("event-type").textContent =
                typeLabels[type] || "Lainnya";

            document.getElementById("event-time").textContent =
                `${start} - ${end}`;

            document.getElementById("event-description").textContent =
                description || "Tidak ada deskripsi.";

            document.getElementById("event-fixed").textContent = isFixed
                ? "🔒 Jadwal tetap"
                : "Jadwal fleksibel";

            // Tombol edit
            document.getElementById("edit-event").href =
                `/calendar/${event.id}/edit`;

            // Form hapus
            document.getElementById("delete-event-form").action =
                `/calendar/${event.id}`;

            // Tampilkan modal
            document.getElementById("event-modal").classList.remove("hidden");
        },
    });

    calendar.render();

    // Tutup modal
    const closeModal = document.getElementById("close-event-modal");

    if (closeModal) {
        closeModal.addEventListener("click", function () {
            document.getElementById("event-modal").classList.add("hidden");
        });
    }
});
