<template>
  <div>
    <Head title="Outlook Accounts" />
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <perPageDropdown />
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="dataTables_filter">
                <Link
                  :href="route('admin.createOutlookAccount')"
                  class="btn btn-primary"
                >
                  <i class="la la-plus"></i>Add Outlook Account
                </Link>
              </div>
            </div>
          </div>
          <div class="row table-responsive table_fixed_width">
            <div class="col-sm-12">
              <table
                class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer"
              >
                <thead>
                  <tr>
                    <th style="width: 30%">Email</th>
                    <th style="width: 15%">Daily Limit</th>
                    <th style="width: 15%">Sent Today</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 25%">Actions</th>
                  </tr>
                  <tr class="filter">
                    <th>
                      <input
                        type="search"
                        v-model="form.email"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th></th>
                    <th></th>
                    <th>
                      <select
                        v-model="form.status"
                        class="form-control form-control-sm form-filter"
                      >
                        <option value="">All Status</option>
                        <option value="queued">Queued</option>
                        <option value="sent">Sent</option>
                      </select>
                    </th>
                    <th>
                      <div class="d-flex gap-2">
                        <button
                          class="btn btn-brand kt-btn btn-sm button-fx flex-fill"
                          @click="search"
                        >
                          <i class="la la-search"></i> Search
                        </button>
                        <button
                          class="btn btn-secondary kt-btn btn-sm button-fx flex-fill"
                          @click="resetSearch"
                        >
                          <i class="la la-close"></i> Reset
                        </button>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="account in accounts?.data" :key="account.id">
                    <td>{{ account.email }}</td>
                    <td>{{ account.daily_limit }}</td>
                    <td>{{ account.sent_today }}</td>
                    <td class="align-center">
                      <span
                        class="kt-badge kt-badge--inline kt-badge--pill cursor-pointer"
                        :class="
                          account.status === 'queued'
                            ? 'kt-badge--success'
                            : 'kt-badge--warning'
                        "
                      >
                        {{ account.status === "queued" ? "Queued" : "Sent" }}
                      </span>
                    </td>

                    <!-- <td class="align-center">
                      <span
                        class="kt-badge kt-badge--inline kt-badge--pill cursor-pointer"
                        :class="
                          user.active == 1
                            ? 'kt-badge--success'
                            : 'kt-badge--warning'
                        "
                        >{{ user.active == 1 ? "Active" : "Inactive" }}</span
                      >
                    </td> -->
                    <td>
                      <div class="dropdown">
                        <button
                          class="btn btn-sm btn-clean btn-icon"
                          data-toggle="dropdown"
                        >
                          <i class="la la-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                          <Link
                            class="dropdown-item"
                            :href="
                              route('admin.editOutlookAccount', account.id)
                            "
                          >
                            <i class="la la-edit"></i> Edit
                          </Link>
                          <button
                            class="dropdown-item"
                            @click="resetCount(account.id)"
                          >
                            <i class="la la-refresh"></i> Reset Count
                          </button>
                          <button
                            class="dropdown-item"
                            @click="deleteAccount(account.id)"
                          >
                            <i class="fa fa-trash"></i> Delete
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-sm-12" v-if="accounts?.total == 0">
              <div class="no_data text-center">
                <h3>No Outlook Accounts Found</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-5">
            <div class="dataTables_info">
              Showing {{ accounts?.from || 0 }} to {{ accounts?.to || 0 }} of
              {{ accounts?.total || 0 }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="float-right">
              <Bootstrap4Pagination
                :data="accounts"
                @pagination-change-page="ListHelper.setPageNum"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted, reactive } from "vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import ListHelper from "@/helpers/ListHelper";
import perPageDropdown from "@/components/PerpageDropdown.vue";
import { pickBy } from "lodash";

const props = defineProps({
  accounts: Object,
});

const params = () => new URLSearchParams(window.location.search);

const form = reactive({
  email: params().get("email") || null,
  status: params().get("status") || null,
});

onMounted(() => {
  emit.emit("pageName", "Email Management", [
    {
      title: "Outlook Accounts",
      routeName: "admin.outlookAccounts",
    },
  ]);
});

const search = () => {
  router.visit(route("admin.outlookAccounts"), {
    method: "get",
    data: pickBy(form),
    preserveScroll: true,
  });
};

const resetSearch = () => {
  router.visit(route("admin.outlookAccounts"), {
    method: "get",
  });
};

const deleteAccount = (id) => {
  sw.confirm("deleteConfirm", id);
};

const resetCount = (id) => {
  router.post(route("admin.resetSentCount", id));
};

emit.on("deleteConfirm", function (id) {
  router.delete(route("admin.deleteOutlookAccount", id));
});
</script>
