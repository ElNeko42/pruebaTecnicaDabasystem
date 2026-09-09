import { defineStore } from "pinia";
import axios from "axios";
export const useRoomTypesStore = defineStore("roomTypes", {
    state: () => ({ items: [], loading: false, saving: false, error: "" }),
    actions: {
        async fetch() {
            this.loading = true;
            try {
                this.items = (await axios.get("/admin/room-types")).data.data;
            } finally {
                this.loading = false;
            }
        },
        async save(form) {
            this.saving = true;
            this.error = "";
            try {
                const response = form.id
                    ? await axios.put(`/admin/room-types/${form.id}`, form)
                    : await axios.post("/admin/room-types", form);
                const item = response.data.data;
                const index = this.items.findIndex((x) => x.id === item.id);
                if (index >= 0) this.items[index] = item;
                else this.items.push(item);
            } catch (e) {
                const errors = e.response?.data?.errors;
                this.error = errors
                    ? Object.values(errors).flat().join(" ")
                    : e.response?.data?.message ||
                      "No se pudo guardar el tipo de habitación.";
                throw e;
            } finally {
                this.saving = false;
            }
        },
    },
});
