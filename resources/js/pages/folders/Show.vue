<template lang="">
    <div class="mb-4 breadcrumb">
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
                    <BreadcrumbSeparator/>
                </template>
                <BreadcrumbItem>
                    <BreadcrumbPage>{{ current_folder_name }}</BreadcrumbPage>
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
            current_folder_name: "",
            columns: [
                {
                    name: "name",
                    label: $('name'),
                    class: "text-left font-semibold",
                    has_icon: true,
                },
                {
                    name: "created_by",
                    label: $t("created_by"),
                },
                {
                    name: "modified",
                    label: $t("modified"),
                },
                {
                    name: "size",
                    label: $t("size"),
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
        this.fetchData();

        // Watch for route changes
        this.$watch("$route.params.id", (newId, oldId) => {
            if (newId !== oldId) {
                this.id = newId;
                this.fetchData();
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
            getBreadcrumb(this.id).then((res) => {
                if (res.status === 200) {
                    this.breadcrumb = res.data.data;
                    this.current_folder_name = res.data.current_folder_name;
                }
            });
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
            } else {
                this.$root.showPreview(row);
            }
        },
    },
};
</script>
