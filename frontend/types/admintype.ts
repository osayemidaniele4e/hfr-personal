export interface TalentStats {
  statusCode: string;
  talentStats: {
    totalTalents: number;
    unverifiedTalents: number;
    incompleteProfile: number;
  };
}
export interface CompanyStats {
  statusCode: string;
  companyStats: {
    totalCompany: number;
  };
}
export interface ReferralStats {
  statusCode: string;
  referralStats: {
    totalReferrals: number;
    pendingReferrals: number;
    approvedReferrals: number;
    rejectedReferrals: number;
  };
}
export interface CompanyTalentRequestStats {
  statusCode: string;
  noOfTalentReq: number;
  talentRequests: [
    {
      id: string;
      companyId: string;
      companyName: string;
      role: string;
      skills: string[];
      jobType: string;
      talentLocation: string;
      numberOfResources: number;
      levelOfExpertise: number;
      yearsOfExperience: number;
      additionalTalentService: string;
      status: number;
    }
  ];
}
export interface notificationStats {
  statusCode: string;
  newNotifications: [
    {
      img: string;
      id: string;
      message: string;
      status: string;
      createdAt: string;
      updatedAt: string;
    }
  ];
  numberOfNewNotifications: number | undefined;
  oldNotifications: [
    {
      id: string;
      message: string;
      status: string;
      createdAt: string;
      updatedAt: string;
    }
  ];
}
export interface AllCompany {
  companies: [
    {
      id: string;
      companyId: string;
      companySize: number;
      companyIndustry: string;
      companyState: string;
      companyCountry: string;
      companyAddress: string;
      company_info: {
        id: string;
        fullName: string;
        companyName: string;
        companyEmail: string;
        companyPhoneNum: string;
      };
    }
  ];
}
export interface AllTalent {
  statusCode: string;
  noOfTalents: number;
  talents: [
    {
      id: string;
      talentId: string;
      profilePicUrl: string;
      mainService: string;
      skills: string[];
      levelOfExpertise: number;
      yearsOfExperience: string;
      location: string;
      description: string;
      profileStatusIndex: string | number;
      linkedInUrl: string;
      githubUrl: string;
      websiteUrl: null;
      talent_info: {
        fullName: string;
        id: string;
        talentEmail: string;
        talentPhoneNum: string;
      };
      employmentHistory: [
        {
          role: string;
          company: string;
          endDate: string;
          jobType: string;
          industry: string;
          startDate: string;
          jobDescription: string;
        }
      ];
      education: [
        {
          school: string;
          endDate: string;
          startDate: string;
          certificate: string;
          fieldOfStudy: string;
          formattedDates: string[];
        }
      ];
    }
  ];
}

export interface Talent {
  id: string;
  talentId: string;
  profilePicUrl: string;
  mainService: string;
  skills: string[];
  levelOfExpertise: number;
  yearsOfExperience: string;
  location: string;
  description: string;
  profileStatusIndex: number;
  linkedInUrl: string;
  githubUrl: string;
  websiteUrl: null;
  talent_info: {
    fullName: string;
    id: string;
    talentEmail: string;
    talentPhoneNum: string;
  };
  employmentHistory: [
    {
      role: string;
      company: string;
      endDate: string;
      jobType: string;
      industry: string;
      startDate: string;
      jobDescription: string;
    }
  ];
  education: [
    {
      school: string;
      endDate: string;
      startDate: string;
      certificate: string;
      fieldOfStudy: string;
      formattedDates: string[];
    }
  ];
}

export interface AllReferral {
  talentReferral: [
    {
      id: string;
      talentRequestId: string;
      talentName: string;
      talentId: string;
      companyName: string;
      jobType: string;
      jobRole: string;
      talentLocation: string;
      levelOfExpertise: string;
      status: number;
    }
  ];
}

export interface companyTalentRequest {
  id: string;
  companyId: string;
  companyName: string;
  role: string;
  skills: string[];
  jobType: string;
  talentLocation: string;
  numberOfResources: number;
  levelOfExpertise: number;
  yearsOfExperience: number;
  additionalTalentService: string;
  status: number;
}
