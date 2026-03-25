declare module "react-data-table-component-extensions" {
  import { ComponentType } from "react";
  import { TableProps } from "react-data-table-component";

  export interface DataTableExtensionsProps extends TableProps<any> {
    export?: boolean;
    print?: boolean;
    filter?: boolean;
    filterPlaceholder?: string;
  }

  const DataTableExtensions: ComponentType<DataTableExtensionsProps>;

  export default DataTableExtensions;
}
