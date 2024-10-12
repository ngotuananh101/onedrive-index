<template>
    <div
        class="sticky top-0 mb-5 text-black bg-neutral-100 dark:bg-neutral-900 dark:text-white"
        id="content-header"
    >
        <h1
            class="text-xl text-left md:text-2xl text-neutral-900 dark:text-white"
            v-if="!show_header"
        >
            Chào mừng bạn đến với Drive
        </h1>
        <h1
            class="mb-5 text-xl text-center md:text-2xl text-neutral-900 dark:text-white"
            v-if="show_header"
        >
            Chào mừng bạn đến với Drive
        </h1>
        <div class="flex justify-center" v-if="scroll_y == 0">
            <div class="relative w-full md:w-[50%] lg:w-[35%]">
                <Input
                    id="search"
                    type="text"
                    :placeholder="$t('search')"
                    class="pl-10 rounded-full"
                    v-model="this.$parent.$parent.$parent.search"
                />
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 start-2"
                >
                    <i class="fa-regular fa-magnifying-glass"></i>
                </span>
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 end-2"
                    @click="this.$parent.$parent.$parent.search = ''"
                    v-if="this.$parent.$parent.$parent.search"
                >
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
        </div>
    </div>
    <DataTable :data="table_data" :columns="columns" :loading="loading" @sort="sort" />
</template>
<script>
import { Input } from "@/components/ui/input";
import DataTable from "../../components/DataTable.vue";
import { getDriveRoot } from "../../services/driveService";

export default {
    name: "Home",
    components: {
        Input,
        DataTable,
    },
    data() {
        return {
            scroll_y: 0,
            show_header: true,
            loading: false,
            table_data: [],
            columns: [
                {
                    name: "name",
                    label: "Tên",
                    sortable: "asc",
                    class: "text-left font-bold",
                    has_icon: true,
                },
                {
                    name: "modified",
                    label: "Lần sửa đổi cuối",
                },
                {
                    name: "size",
                    label: "Kích cỡ tệp",
                },
            ],
        };
    },
    mounted() {
        this.loading = true;
        getDriveRoot().then((res) => {
            console.log(res);
            res.status == 200 ? this.table_data = [...this.table_data, ...res.data.data] : null;
            console.log(this.table_data);

            this.loading = false;
        });
    },
    watch: {
        "$parent.$parent.$parent.$data.scroll_y": function (scroll_y) {
            this.scroll_y = scroll_y;
            let element = document.getElementById("content-header");
            let height = 0;
            if (element) {
                height = element.offsetHeight;
            }
            if (scroll_y > height) {
                this.show_header = false;
            } else {
                this.show_header = true;
            }
        },
    },
    methods: {
        sort(column) {
            console.log(column);
        },
    },
};
</script>
<style lang=""></style>
