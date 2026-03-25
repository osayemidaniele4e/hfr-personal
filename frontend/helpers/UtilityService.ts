export interface CourseData {
  email: string;
  course: string;
  fullName: string;
}

const useUtilityService = () => {
  const setCourseToLocalStorage = (data: CourseData) => {
    if (typeof window !== "undefined") {
      localStorage.setItem("course", JSON.stringify(data));
    }
  };

  const getCourseFromLocalStorage = (): CourseData | null => {
    if (typeof window !== "undefined") {
      const courseItems = localStorage.getItem("course");
      return courseItems ? JSON.parse(courseItems) : null;
    }
    return null;
  };

  return {
    setCourseToLocalStorage,
    getCourseFromLocalStorage,
  };
};

export default useUtilityService;
