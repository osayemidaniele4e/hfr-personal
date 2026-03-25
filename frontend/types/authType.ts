import { Dispatch, SetStateAction } from "react";
import { CookieValueTypes } from "cookies-next";
interface TalentData {
  mainService: string;
  id: string;
  fullName: string;
  talentEmail: string;
  talentPhoneNum: string;
}
export interface ResetPassword {
  emailaddress: string;
}
export interface PasswordChange {
  token: string | undefined;
  password: string | undefined;
  confirmPassword: string | undefined;
}
interface CompanyData {
  id: string;
  fullName: string;
  email: string;
  phoneNumber: string;
}
export type UserType = {
  id: number;
  name: string;

  token: string;
  talentData: TalentData;
  companyData: CompanyData;
};

export type LoginRequestType = {
  username: string | undefined;
  password: string | undefined;
};

export interface AuthPayLoad {
  message: string | undefined;
  payload: {
    accessToken: string;
    refreshToken: string;
  };
}

export type RegistrationRequestType = {
  email: string | undefined;
  username: string | undefined;
  firstName: string | undefined;
  lastName: string | undefined;
  password: string | undefined;
  confirmPassword: string | undefined;
  role: string | undefined;
};

export type RegisterPayloadType = {
  fullName: string;
  setFullName: Dispatch<SetStateAction<string>>;
  companyName: string;
  setCompanyName: Dispatch<SetStateAction<string>>;
  companyEmail: string;
  setCompanyEmail: Dispatch<SetStateAction<string>>;
  companyPhoneNum: string;
  setCompanyPhoneNum: Dispatch<SetStateAction<string>>;
  password: string;
  setPassword: Dispatch<SetStateAction<string>>;
  confirmpassword: string;
  setConfirmPassword: Dispatch<SetStateAction<string>>;
  isLoading: boolean;
};
export interface RegisterTalentPayloadType {
  fullName: string;
  setFullName: Dispatch<SetStateAction<string>>;
  password: string;
  setPassword: Dispatch<SetStateAction<string>>;
  confirmpassword: string;
  setConfirmPassword: Dispatch<SetStateAction<string>>;
  isLoading: boolean;
  talentEmail: string;
  setTalentEmail: Dispatch<SetStateAction<string>>;
  talentPhoneNum: string;
  setTalentPhoneNum: Dispatch<SetStateAction<string>>;
}

export interface TalentLoginForm {
  talentEmail: string;
  password: string;
}

export interface AdminLoginForm {
  email: string;
  password: string;
}

export interface TalentUpdate {
  talentId: string | CookieValueTypes;
  portfolio: {
    mainService: string;
    skills: string[];
    levelOfExpertise: string;
    yearsOfExperience: string;
    location: string;
    description: string;
    linkedInUrl: string;
    githubUrl: string;
    otherProfileUrl: string;
  };
  // resourceDetails: {
  //   currentEmploymentStatus: string;
  //   nameOfCompany: string;
  //   companyIndustry: string;
  //   jobTitle: string;
  //   periodOfEmployment: string[];
  //   jobRoleDescription: string;
  //   institutionAttended: string;
  //   areaOfStudy: string;
  //   degreeType: string;
  //   periodOfStudy: string[];
  //   socials:string[],
  //   websiteUrl:string;

  // }

  // profilePic: File | string;
}

export interface profileEdit {
  talentId: string | CookieValueTypes;

  updateFields: {
    fullName: string;
    talentEmail: string;
    talentPhoneNum: string;

    description: string;

    // mainService: string;
    // skills: string[];
    //  levelOfExpertise: string;
    // yearsOfExperience: string;
    // location: string;

    // currentEmploymentStatus: string;
    // nameOfCompany: string;
    // companyIndustry: string;
    // jobTitle: string;
    // periodOfEmployment: string[];
    // jobRoleDescription: string;
    // institutionAttended: string;
    // areaOfStudy: string;
    // degreeType: string;
    // periodOfStudy: string[];
    // socials:string[],
    // websiteUrl:string;
  };

  // profilePic: File | string;
}

export interface portfolioEdit {
  talentId: string | CookieValueTypes;
  updateFields: {
    mainService: string;
    skills: string[];
    levelOfExpertise: string;
    otherProfileUrl: string;
    linkedInUrl: string;
    githubUrl: string;
  };
}
export interface employmentHistoryEdit {
  talentId: string | CookieValueTypes;
  updateFields: {
    employmentHistory: object[];
    // role: string;
    // company: string;
    // endDate: string;
    // jobType: string;
    // industry: string;
    // startDate: string;
    // jobDescription: string;
  };
}
export interface educationEdit {
  talentId: string | CookieValueTypes;
  updateFields: {
    education: object[];
    // role: string;
    // company: string;
    // endDate: string;
    // jobType: string;
    // industry: string;
    // startDate: string;
    // jobDescription: string;
  };
}

export interface companyLoginForm {
  companyEmail: string;
  password: string;
}
export interface talentRegisterForm {
  fullName: string;
  password: string;
  confirmpassword: string;
  talentEmail: string;
  talentPhoneNum: string;
}
export interface talentforgotpassForm {
  talentEmail: string;
}
export interface companyRegisterForm {
  fullName: string;
  email: string;
  phoneNum: string;
  course: string;
}
export interface companyforgotpassForm {
  companyEmail: string;
}

export interface companyRequestForm {
  companyEmail: string;
  role: string;
  companyId: string | CookieValueTypes;
  companyName: string;
  skills: string[];
  jobType: string;
  talentLocation: string;
  numberOfResources: number;
  levelOfExpertise: string;
  yearsOfExperience: number;
  additionalTalentService: string;
}
export interface companyEditRequestForm {
  role: string;
  skills: string[];
  jobType: string;
  talentLocation: string;
  numberOfResources: number;
  levelOfExpertise: string;
  yearsOfExperience: number;
  additionalTalentService: string;
}

interface EmploymentHistoryItem {
  role: string;
  company: string;
  endDate: string;
  jobType: string;
  industry: string;
  startDate: string;
  jobDescription: string;
  formattedDates: Array<string>;
}
export interface ProfileInfo {
  profileInfo: {
    id: string;
    profilePicUrl: string;
    mainService: string;
    skills: string[];
    levelOfExpertise: string;
    yearsOfExperience: string;
    location: string;
    description: string;
    profileStatusIndex: number | null;
    linkedInUrl: string;
    githubUrl: string;
    otherProfileUrl: string | null;
    employmentHistory: any[];
    education: any[];
    talentId: string;
    createdAt: string;
    updatedAt: string;
    percentageComplete: number;
  };
}

export interface Session {
  // Define session-related properties here
  talentData: {
    token: string;
    id: string;
    fullName: string;
    talentEmail: string;
    talentPhoneNum: string;
  };

  // Add other fields as necessary
}

export interface profileContextType {
  data: ProfileInfo | null;
  session: Session | null;
}
