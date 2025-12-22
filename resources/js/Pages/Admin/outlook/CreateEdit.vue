<template>
  <div>
    <Head
      :title="account ? 'Edit Outlook Account' : 'Create Outlook Account'"
    />
    <div class="kt-portlet">
      <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
          <h3 class="kt-portlet__head-title">
            {{ account ? "Edit Outlook Account" : "Create Outlook Account" }}
          </h3>
        </div>
      </div>
      <form @submit.prevent="submit">
        <div class="kt-portlet__body">
          <div class="form-group">
            <label class="label-required">Email Address</label>
            <input
              type="email"
              v-model="form.email"
              class="form-control"
              :class="{ 'is-invalid': errors.email }"
              placeholder="Enter email address"
              required
            />
            <div v-if="errors.email" class="invalid-feedback">
              {{ errors.email }}
            </div>
          </div>

          <div class="form-group">
            <label class="label-required">Daily Limit</label>
            <input
              type="number"
              v-model="form.daily_limit"
              class="form-control"
              :class="{ 'is-invalid': errors.daily_limit }"
              placeholder="Enter daily email limit"
              min="1"
              max="1000"
              required
            />
            <div v-if="errors.daily_limit" class="invalid-feedback">
              {{ errors.daily_limit }}
            </div>
            <small class="form-text text-muted"
              >Maximum emails that can be sent per day (1-{{
                form.daily_limit
              }})</small
            >
          </div>

          <div class="form-group" v-if="account">
            <label>Sent Today</label>
            <input
              type="number"
              v-model="form.sent_today"
              class="form-control"
              :class="{ 'is-invalid': errors.sent_today }"
              placeholder="Current sent count"
              min="0"
            />
            <div v-if="errors.sent_today" class="invalid-feedback">
              {{ errors.sent_today }}
            </div>
          </div>

          <!-- <div class="form-group">
            <label class="label-required">Status</label>
            <select
              v-model="form.status"
              class="form-control"
              :class="{ 'is-invalid': errors.status }"
              required
            >
              <option value="">Select Status</option>
              <option value="queued">Queued (Ready to send)</option>
              <option value="sent">Sent (Reached daily limit)</option>
            </select>
            <div v-if="errors.status" class="invalid-feedback">
              {{ errors.status }}
            </div>
          </div> -->
        </div>

        <div class="kt-portlet__foot">
          <div class="kt-form__actions">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="processing"
            >
              <span v-if="processing">
                <i class="fa fa-spinner fa-spin"></i> Processing...
              </span>
              <span v-else>
                <i class="la la-check"></i>
                {{ account ? "Update Account" : "Create Account" }}
              </span>
            </button>
            &nbsp;
            <Link
              :href="route('admin.outlookAccounts')"
              class="btn btn-secondary"
            >
              <i class="la la-arrow-left"></i> Back to List
            </Link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
  account: Object,
  errors: Object,
});

const form = useForm({
  email: props.account?.email || "",
  daily_limit: props.account?.daily_limit || 50,
  sent_today: props.account?.sent_today || 0,
  status: props.account?.status || "queued",
});

onMounted(() => {
  const title = props.account
    ? "Edit Outlook Account"
    : "Create Outlook Account";
  emit.emit("pageName", "Email Management", [
    {
      title: "Outlook Accounts",
      routeName: "admin.outlookAccounts",
    },
    {
      title: title,
    },
  ]);
});

const submit = () => {
  if (props.account) {
    form.post(route("admin.editOutlookAccount", props.account.id));
  } else {
    form.post(route("admin.createOutlookAccount"));
  }
};
</script>
