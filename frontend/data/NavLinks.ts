export interface NavLinksType {
  navitem: string;
  subMenus?: { menu: string; link: string }[];
  link: string;
}

export const NavLinks = [
  {
    navitem: "Home",
    link: "/",
  },
  {
    navitem: "About",
    link: "/about",
  },
  {
    navitem: "Contact Us",
    link: "/contact-us",
  },
  {
    navitem: "API",
    link: "/developers",
  },
  // {
  //   navitem: "Finders",
  //   link: "/facilitieslist",
  // },
];

export const navData = [
  { navitem: "Overview", link: "/studentdashboard/overview" },
  { navitem: "Browse", link: "/studentdashboard/browse" },
  { navitem: "My Learnings", link: "/studentdashboard/mylearning" },
  { navitem: "Mentors", link: "/studentdashboard/mentors" },
  { navitem: "Community", link: "/studentdashboard/community" },
  { navitem: "Job board", link: "/studentdashboard/jobboard" },
];

export const AdminNavLinks = [
  {
    navitem: "Home",
    link: "/admin-dashboard/home",
    icon: "/contacts-line.svg",
  },
  {
    navitem: "Company",
    link: "/admin-dashboard/company",
    icon: "/company-icon.svg",
  },
  {
    navitem: "Talent Request",
    link: "/admin-dashboard/talent-request",
    icon: "/talent-icon.svg",
  },
  {
    navitem: "Notification",
    link: "/admin-dashboard/notification",
    icon: "/notification-icon.svg",
    notify: "3",
  },
  {
    navitem: "Referral",
    link: "/admin-dashboard/referral",
    icon: "/referral-icon.svg",
  },
];

export const LoginData = [
  {
    navitem: "Login",
    subMenus: [
      { menu: "For Talent", link: "/talent-login" },
      { menu: "For Company", link: "/company-login" },
    ],
    link: "",
  },
];
