<template lang="">
    <div class="p-3 mb-4 md:p-0 breadcrumb">
        <Breadcrumb>
            <BreadcrumbList>
                <template v-for="(item, index) in breadcrumb" :key="index">
                    <BreadcrumbItem>
                        <BreadcrumbLink as-child>
                            <template v-if="index >= 1">
                                <router-link
                                    :to="{
                                        name: 'folders.show',
                                        params: { id: item.id },
                                    }"
                                    @click="updateBreadcrumb(index, item)"
                                >
                                    {{ item.name }}
                                </router-link>
                            </template>
                            <template v-else>
                                <router-link
                                    :to="{
                                        name: 'index',
                                    }"
                                >
                                    Home
                                </router-link>
                            </template>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                </template>
                <BreadcrumbItem>
                    <BreadcrumbPage>{{ current_folder.name }}</BreadcrumbPage>
                </BreadcrumbItem>
            </BreadcrumbList>
        </Breadcrumb>
    </div>
    <FileTable
        :data="table_data"
        :columns="columns"
        :loading="loading"
        @onScrollEvent="onScrollEvent"
        @rowClick="rowClick"
    />
</template>

<script>
import FileTable from "../../components/FileTable.vue";
import { getFolderById, getBreadcrumb } from "../../services/driveService";
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";

export default {
    components: {
        FileTable,
        Breadcrumb,
        BreadcrumbItem,
        BreadcrumbLink,
        BreadcrumbList,
        BreadcrumbPage,
        BreadcrumbSeparator,
    },
    data() {
        return {
            id: this.$route.params.id,
            scroll_y: 0,
            is_end: 0,
            loading: false,
            table_data: [],
            breadcrumb: [],
            current_folder: "",
            columns: [
                {
                    name: "name",
                    label: this.$t("name"),
                    class: "text-left font-semibold",
                    has_icon: true,
                },
                {
                    name: "created_by",
                    label: this.$t("created_by"),
                },
                {
                    name: "modified",
                    label: this.$t("modified"),
                },
                {
                    name: "size",
                    label: this.$t("size"),
                },
            ],
            query: {
                per_page: import.meta.env.VITE_ONE_DRIVE_PER_PAGE,
                next_url: "",
            },
            total: 0,
        };
    },
    mounted() {
        this.fetchBreadcrumb();
        this.$parent.$parent.scroll_y = 0;
        this.$parent.$parent.show_search = true;
        this.$root.need_reload_breadcrumb = false;
        this.fetchData();

        // Watch for route changes
        this.$watch("$route.params.id", (newId, oldId) => {
            if (newId !== oldId) {
                this.id = newId;
                this.table_data = [];
                this.$parent.$parent.scroll_y = 0;
                this.fetchData();
                this.$root.need_reload_breadcrumb ? this.fetchBreadcrumb() : "";
            }
        });
    },
    methods: {
        fetchData() {
            this.loading = true;
            getFolderById(this.query, this.id).then((res) => {
                if (res.status === 200) {
                    this.table_data = res.data.data;
                    this.query.next_url = res.data.next_url;
                    this.query.next_url ? (this.is_end = 1) : (this.is_end = 0);
                }
                this.loading = false;
            });
        },
        fetchBreadcrumb() {
            this.breadcrumb = [];
            this.current_folder = "";
            getBreadcrumb(this.id).then((res) => {
                if (res.status === 200) {
                    this.breadcrumb = res.data.data;
                    this.current_folder = res.data.current_folder;
                }
            });
            this.$root.need_reload_breadcrumb = false;
        },
        loadMore() {
            if (this.query.next_url && !this.loading && this.is_end === 1) {
                this.loading = true;
                getDriveRoot(this.query).then((res) => {
                    if (res.status === 200) {
                        this.table_data = [
                            ...this.table_data,
                            ...res.data.data,
                        ];
                        this.query.next_url = res.data.next_url;
                        this.query.next_url
                            ? (this.is_end = 1)
                            : (this.is_end = 0);
                    }
                    this.loading = false;
                });
            }
        },
        onScrollEvent(event) {
            this.scroll_y = event.srcElement.scrollTop;
            this.$parent.$parent.scroll_y = this.scroll_y;
            const viewportHeight = event.srcElement.clientHeight;
            if (
                this.scroll_y >=
                event.srcElement.scrollHeight - viewportHeight - 20
            ) {
                this.is_end = 1;
            }
            this.loadMore();
        },
        rowClick(row) {
            if (row.type === "folder") {
                this.$router.push({
                    name: "folders.show",
                    params: { id: row.id },
                });
                this.breadcrumb = [...this.breadcrumb, this.current_folder];
                this.current_folder = {
                    id: row.id,
                    name: row.name,
                };
            } else {
                this.$root.showPreview(row);
            }
        },
        updateBreadcrumb(index, item){
            this.breadcrumb = this.breadcrumb.slice(0, index);
            this.current_folder = item;
        }
    },
};
</script>
