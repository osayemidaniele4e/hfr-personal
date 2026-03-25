import { AuthPayLoad } from "@/types/authType";
import { apiSlice } from "../slices/apiSlice";

export const authApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    talentLogin: builder.mutation({
      query: (data) => ({
        url: "api/v1/auth/login",
        method: "POST",
        body: data,
      }),
    }),
    companyLogin: builder.mutation({
      query: (data) => ({
        url: "company/auth/login",
        method: "POST",
        body: data,
      }),
    }),
    talentRegister: builder.mutation({
      query: (data) => ({
        url: "api/v1/auth/register",
        method: "POST",
        body: data,
      }),
    }),
    passwordChangeNotification: builder.mutation({
      query: (data) => ({
        url: "api/v1/auth/password-change/notify",
        method: "POST",
        body: data,
      }),
    }),
    passwordChange: builder.mutation({
      query: (data) => ({
        url: "api/v1/auth/password-change",
        method: "POST",
        body: data,
      }),
    }),
    emailConfirmation: builder.query({
      query: (auth_token) => {
        if (!auth_token) {
          throw new Error("auth_token is required");
        }

        return {
          url: `api/v1/auth/confirm_email?v_tokens=${encodeURIComponent(
            auth_token
          )}`,
        };
      },
    }),
    companyRegister: builder.mutation({
      query: (data) => ({
        url: "company/auth/signup",
        method: "POST",
        body: data,
      }),
    }),
    talentSendOtp: builder.mutation({
      query: (data) => ({
        url: "talent/auth/signup/send_otp",
        method: "POST",
        body: data,
      }),
    }),
    companySendOtp: builder.mutation({
      query: (data) => ({
        url: "company/auth/signup/send_otp",
        method: "POST",
        body: data,
      }),
    }),
    talentValidateOtp: builder.mutation({
      query: (data) => ({
        url: "talent/auth/validate_otp",
        method: "POST",
        body: data,
      }),
    }),
    companyValidateOtp: builder.mutation({
      query: (data) => ({
        url: "company/auth/validate_otp",
        method: "POST",
        body: data,
      }),
    }),
    talentforgotPasswordSendOtp: builder.mutation({
      query: (data) => ({
        url: "talent/auth/forgot_password/send_otp",
        method: "POST",
        body: data,
      }),
    }),
    talentForgotPasswordReset: builder.mutation({
      query: (data) => ({
        url: "talent/auth/forgot_password",
        method: "POST",
        body: data,
      }),
    }),
    companyforgotPasswordSendOtp: builder.mutation({
      query: (data) => ({
        url: "company/auth/forgot_password/send_otp",
        method: "POST",
        body: data,
      }),
    }),
    companyForgotPasswordReset: builder.mutation({
      query: (data) => ({
        url: "company/auth/forgot_password",
        method: "POST",
        body: data,
      }),
    }),
  }),
});

export const {
  usePasswordChangeNotificationMutation,
  usePasswordChangeMutation,
  useEmailConfirmationQuery,

  useTalentLoginMutation,
  useCompanyLoginMutation,
  useTalentRegisterMutation,
  useCompanyRegisterMutation,
  useTalentSendOtpMutation,
  useCompanySendOtpMutation,
  useTalentValidateOtpMutation,
  useCompanyValidateOtpMutation,
  useCompanyForgotPasswordResetMutation,
  useTalentForgotPasswordResetMutation,
  useCompanyforgotPasswordSendOtpMutation,
  useTalentforgotPasswordSendOtpMutation,
} = authApiSlice;
