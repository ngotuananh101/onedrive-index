<template>
  <div
    class="sticky top-0 w-full text-black bg-neutral-100 dark:bg-neutral-900 dark:text-white"
    :class="{ 'mb-5': show_header }"
    id="content-header"
  >
    <h1
      class="mb-5 text-xl md:text-2xl text-neutral-900 dark:text-white bg-neutral-100 dark:bg-neutral-900"
      :class="{ 'text-left': !show_header, 'text-center': show_header }"
    >
      Chào mừng bạn đến với Drive
    </h1>
    <div class="flex justify-center" v-if="scroll_y === 0">
      <div class="relative w-full md:w-[50%] lg:w-[35%]">
        <Input
          id="search"
          type="text"
          :placeholder="$t('search')"
          class="pl-10 rounded-full"
          v-model="searchModel"
        />
        <span class="absolute inset-y-0 flex items-center justify-center px-2 start-2">
          <i class="fa-regular fa-magnifying-glass"></i>
        </span>
        <span
          class="absolute inset-y-0 flex items-center justify-center px-2 end-2"
          @click="clearSearch"
          v-if="searchModel"
        >
          <i class="fa-solid fa-circle-xmark"></i>
        </span>
      </div>
    </div>
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
import { Input } from "@/components/ui/input";
import FileTable from "../../components/FileTable.vue";
import { getDriveRoot } from "../../services/driveService";

export default {
  name: "Home",
  components: {
    Input,
    FileTable,
  },
  data() {
    return {
      scroll_y: 0,
      is_end: 0,
      show_header: true,
      loading: false,
      table_data: [],
      columns: [
        {
          name: "name",
          label: "Tên",
          class: "text-left font-semibold",
          has_icon: true,
        },
        {
          name: "created_by",
          label: "Chủ sở hữu",
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
      query: {
        per_page: import.meta.env.VITE_ONE_DRIVE_PER_PAGE,
        next_url: "",
      },
      total: 0,
      searchModel: "",
    };
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    searchModel(value) {
      this.$parent.$parent.$parent.search = value;
    },
  },
  methods: {
    fetchData() {
      this.loading = true;
      getDriveRoot(this.query).then((res) => {
        if (res.status === 200) {
          this.table_data = res.data.data;
          this.query.next_url = res.data.next_url;
          this.query.next_url ? (this.is_end = 1) : (this.is_end = 0);
        }
        this.loading = false;
      });
    },
    toggleHeader() {
      const element = document.getElementById("content-header");
      const height = element ? element.offsetHeight : 0;
      this.show_header = this.scroll_y <= height;
      if (this.show_header) {
        this.$emit("toggleHeader", true);
      }
    },
    loadMore() {
      if (this.query.next_url && !this.loading && this.is_end === 1) {
        this.loading = true;
        getDriveRoot(this.query).then((res) => {
          if (res.status === 200) {
            this.table_data = [...this.table_data, ...res.data.data];
            this.query.next_url = res.data.next_url;
            this.query.next_url ? (this.is_end = 1) : (this.is_end = 0);
          }
          this.loading = false;
        });
      }
    },
    clearSearch() {
      this.searchModel = "";
    },
    onScrollEvent(event) {
      this.scroll_y = event.srcElement.scrollTop;
      const viewportHeight = event.srcElement.clientHeight;
      if (this.scroll_y >= event.srcElement.scrollHeight - viewportHeight - 20) {
        this.is_end = 1;
      }
      this.toggleHeader();
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
<style scoped></style>
