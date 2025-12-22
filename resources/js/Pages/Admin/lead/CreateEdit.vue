<template lang="">
  <div>
    <Head title="Create Lead" v-if="!props.user" />
    <Head title="Edit Lead" v-if="props.user" />

    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <form @submit.prevent="submit">
          <div class="form-group validated row" v-auto-animate>
            <div class="form-group col-lg-6">
              <label for="fullname"
                >Full Name <span class="text-danger">*</span></label
              >
              <input
                type="text"
                id="fullname"
                v-model="form.fullname"
                @input="allowOnlyLetters('fullname', $event)"
                maxlength="50"
                class="form-control border-gray-200"
                placeholder="Full Name"
              />
              <span class="text-danger" v-if="form.errors.fullname">{{
                form.errors.fullname
              }}</span>
            </div>

            <div class="form-group col-lg-6">
              <label for="email"
                >Email <span class="text-danger">*</span></label
              >
              <input
                type="email"
                id="email"
                v-model="form.email"
                @input="emailValidate('email', $event)"
                maxlength="100"
                class="form-control border-gray-200"
                placeholder="Email"
              />
              <span class="text-danger" v-if="form.errors.email">{{
                form.errors.email
              }}</span>
            </div>

            <div class="form-group col-lg-6">
              <label for="phone"
                >Phone <span class="text-danger">*</span></label
              >
              <div
                style="
                  display: flex;
                  justify-content: center;
                  align-items: center;
                "
              >
                <input
                  type="text"
                  autocomplete="new_password"
                  id="phone"
                  v-model="form.phone"
                  @input="allowOnlyNumber('phone', $event)"
                  maxlength="15"
                  class="form-control border-gray-200"
                  placeholder="Phone"
                />
              </div>
              <span class="text-danger" v-if="form.errors.phone">{{
                form.errors.phone
              }}</span>
            </div>

            <div class="form-group col-lg-6">
              <label for="Company"
                >Company <span class="text-danger">*</span></label
              >
              <div>
                <input
                  type="text"
                  autocomplete="new_password"
                  id="company"
                  v-model="form.company"
                  @input="allowOnlyLetters('company', $event)"
                  maxlength="15"
                  class="form-control border-gray-200"
                  placeholder="Company"
                />
              </div>
              <span class="text-danger" v-if="form.errors.company">{{
                form.errors.company
              }}</span>
            </div>

            <div class="form-group col-lg-6">
              <label for="website"
                >Website <span class="text-danger">*</span></label
              >
              <div>
                <input
                  type="text"
                  id="website"
                  v-model="form.website"
                  maxlength="50"
                  class="form-control border-gray-200"
                  placeholder="Website"
                />
              </div>
              <span class="text-danger" v-if="form.errors.website">{{
                form.errors.website
              }}</span>
            </div>

            <div class="form-group col-lg-6">
              <label for="status"
                >Status <span class="text-danger">*</span></label
              >
              <select
                id="status"
                class="form-control border-gray-200"
                v-model="form.status"
              >
                <option value="">Select Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
              <span class="text-danger" v-if="form.errors.status">{{
                form.errors.status
              }}</span>
            </div>
          </div>
          <div class="kt-portlet__foot">
            <div class="kt-form__actions">
              <div class="row">
                <div class="pr-2">
                  <submit-button
                    :disabled="form.processing"
                    :isLoading="form.processing"
                    >Submit</submit-button
                  >
                </div>
                <div>
                  <Link
                    :href="route('admin.leads')"
                    class="btn btn-info btn-secondary"
                    >Cancel</Link
                  >
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, watch, ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import FilePond from "@/components/FilePond.vue";
import FileUpload from "@/components/FileUpload.vue";
import Datepicker from "@/components/Datepicker.vue";
import SubmitButton from "@/components/SubmitButton.vue";

const props = defineProps({
  errors: Object,
  lead: Object,
});

const form = useForm({
  fullname: props?.lead?.name || null,
  email: props.lead?.email || null,
  phone: props.lead?.phone || null,
  company: props.lead?.company || null,
  website: props.lead?.website || null,
  status: props.lead?.active || 1,
});

onMounted(() => {
  const pageTitle = props.lead ? "Edit Lead" : "Create Lead";
  emit.emit("pageName", "Lead Management", [
    {
      title: "Lead List",
      routeName: "admin.leads",
    },
    {
      title: pageTitle,
      routeName: "",
    },
  ]);
});

const submit = () => {
  if (props.lead) {
    form.post(route("admin.editLead", props?.lead?.id));
  } else {
    form.post(route("admin.createLead"));
  }
};

const allowOnlyLetters = (param, e) => {
  let inputValue = e.target.value.replace(/[^a-zA-Z\s]/g, "");
  if (inputValue !== "" && inputValue !== "0") {
    form[param] = inputValue;
  } else {
    form[param] = "";
  }
};

const emailValidate = (param, e) => {
  let inputValue = e.target.value.replace(/[^a-zA-Z0-9._%#.+-@]/g, "");
  if (inputValue !== "" && inputValue !== "0") {
    form[param] = inputValue;
  } else if (
    !inputValue.includes("@") ||
    !inputValue.split("@")[1].includes(".")
  ) {
    form.errors.email = "Invalid email formate";
  } else {
    form[param] = "";
  }
};

const allowOnlyNumber = (param, e) => {
  let inputValue = e.target.value.replace(/\D/g, "");
  if (inputValue !== "" && inputValue !== "0") {
    form[param] = inputValue;
  } else {
    form[param] = "";
  }
};
</script>
