import { apiSlice } from "../../slices/apiSlice";

export const companyRequestApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    // createRequest: builder.mutation({
    //   query: (data) => ({
    //     url: "talent_request/create_request",
    //     method: "POST",
    //     body: data,
    //   }),
    // }),
    // updateRequest: builder.mutation({
    //   query: ({ data, requestid }) => ({
    //     url: `talent_request/update_request/${requestid}`,
    //     method: "PATCH",
    //     body: data,
    //   }),
    // }),
    // updateReferralStatus: builder.mutation({
    //   query: ({ data, requestid }) => ({
    //     url: `talent_referral/update_referral_status/${requestid}`,
    //     method: "PATCH",
    //     body: data,
    //   }),
    // }),
    // getTalentRequests: builder.query({
    //   query: (requestid) => ({
    //     url: `talent_request/get_talent_requests/${requestid}`,
    //   }),
    // }),
    // getTalentThatMatch: builder.query({
    //   query: (requestid) => ({
    //     url: `talent_request/get_talents_that_match_request/${requestid}`,
    //   }),
    // }),
    // getTalentRequestReferrals: builder.query({
    //   query: (requestid) => ({
    //     url: `talent_referral/get_talent_request_referral/${requestid}`,
    //   }),
    // }),
  }),
});

export const {
  // useCreateRequestMutation,
  // useUpdateRequestMutation,
  // useGetTalentRequestsQuery,
  // useGetTalentThatMatchQuery,
  // useUpdateReferralStatusMutation,
  // useGetTalentRequestReferralsQuery,
} = companyRequestApiSlice;
