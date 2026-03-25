import { getCookie } from "cookies-next";
import { apiSlice } from "../slices/apiSlice";
import {
  AllCompany,
  AllReferral,
  AllTalent,
  CompanyStats,
  CompanyTalentRequestStats,
  ReferralStats,
  TalentStats,
  notificationStats,
} from "@/types/admintype";
const token = getCookie("USER");
export const AdminApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getTalentStats: builder.query<TalentStats, void>({
      query: () => ({
        url: `talent/profile/get_talent_stats`,
      }),
    }),
    getAllTalent: builder.query<AllTalent, void>({
      query: () => ({
        url: `talent/get_all_talents`,
      }),
    }),
    updateTalentStatus: builder.mutation({
      query: (data) => ({
        url: `talent/profile/update_details`,
        method: "PATCH",
        body: data,
      }),
    }),
    getAllCompany: builder.query<AllCompany, void>({
      query: () => ({
        url: `company/get_all_companies`,
      }),
    }),
    getCompanyStats: builder.query<CompanyStats, void>({
      query: () => ({
        url: `company/profile/get_company_stats`,
      }),
    }),
    getReferralStats: builder.query<ReferralStats, void>({
      query: () => ({
        url: `talent_referral/get_referral_stats`,
      }),
    }),
    getAllReferral: builder.query<AllReferral, void>({
      query: () => ({
        url: `talent_referral/get_all_referrals`,
      }),
    }),
    getTalentRequestStats: builder.query<CompanyTalentRequestStats, void>({
      query: () => ({
        url: `talent_request/get_all_talent_requests`,
      }),
    }),
    createReferral: builder.mutation({
      query: (data) => ({
        url: "talent_referral/create_referral",
        method: "POST",
        body: data,
      }),
    }),
    exportReferral: builder.mutation({
      query: (data) => ({
        url: "talent_referral/export_referrals",
        method: "POST",
        body: data,
        responseHandler: async (response) => {
          const headers = response.headers;
          const blob = await response.blob();
          return { blob, headers };
        },
      }),
    }),
    getAllNotifications: builder.query<notificationStats, void>({
      query: () => ({
        url: `admin/auth/get_all_notifications?`,
      }),
    }),
    adminLogin: builder.mutation({
      query: (data) => ({
        url: "admin/auth/login",
        method: "POST",
        body: data,
      }),
    }),
    adminDeleteUser: builder.mutation({
      query: ({ type, id }) => ({
        url: `/admin/delete_user/${id}?type=${type}`,
        method: "Delete",
      }),
    }),
  }),
});
export const {
  useAdminDeleteUserMutation,
  useAdminLoginMutation,
  useGetTalentStatsQuery,
  useGetCompanyStatsQuery,
  useGetReferralStatsQuery,
  useGetTalentRequestStatsQuery,
  useCreateReferralMutation,
  useGetAllTalentQuery,
  useGetAllCompanyQuery,
  useGetAllReferralQuery,
  useUpdateTalentStatusMutation,
  useExportReferralMutation,
  useGetAllNotificationsQuery,
} = AdminApiSlice;
