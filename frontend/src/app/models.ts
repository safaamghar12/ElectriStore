export interface Product {
  id: number;
  name: string;
  sku :string;
  price: number;
  stock: number;
  isAvailable: boolean;
  status: 'Available' | 'Low Stock' | 'Out of Stock';
  category: {
    id : number;
    name : string
  }
}

export interface CreateProductRequest {
  name: string;
  sku: string;
  price: number;
  stock: number;
  category: {
    id: number;
  };
}