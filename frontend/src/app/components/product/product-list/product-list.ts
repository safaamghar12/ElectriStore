import { Component , input } from '@angular/core';
import { ProductDetails } from '../product-details/product-details';
import { Product } from '../../../models';


@Component({
  imports: [ProductDetails],
  selector: 'app-product-list',
  styleUrl: './product-list.css',
  templateUrl: './product-list.html',
})
export class ProductList {

  products=input<Product[]>([]);
  errorMessage= input<string>('');
  isLoading =input<boolean>();
  itemsPerPage: number = 5;
  currentPage: number = 1;
  selectedProduct: any = null;

  constructor() {}


get paginatedProducts(): Product[] {
  const startIndex = (this.currentPage - 1) * this.itemsPerPage;

  return this.products().slice(
    startIndex,
    startIndex + this.itemsPerPage
  );
}

get totalPages(): number {
  return Math.ceil(this.products().length / this.itemsPerPage);
}

  editProduct() {
    // Implement the logic to edit the product
   
  }

  deleteProduct() {
    // Implement the logic to delete the product
  } 

 

  viewProduct(product: any): void {
      this.selectedProduct = product;
    }

  closeProductDetails(): void {
    this.selectedProduct = null;
  }

}
