import { ChangeDetectorRef, Component, signal } from '@angular/core';
import { InventoryStats } from '../../shared/inventory-stats/inventory-stats';
import { Toolbar } from '../../shared/toolbar/toolbar';
import { ProductList } from '../../components/product/product-list/product-list';
import { CreateProduct } from '../../components/product/create-product/create-product';
import { ProductApi } from '../../services/product-api';
import { Product } from '../../models';

@Component({
   imports: [InventoryStats, ProductList, Toolbar, CreateProduct],
  selector: 'app-inventory',
  styleUrl: './inventory.css',
  templateUrl: './inventory.html',
})
export class Inventory {

  products : Product[] = [];
  errorMessage: string = '';
  isLoading: boolean =true;
  readonly isCreatingProduct = signal(false);

  constructor(
    private productApi: ProductApi,
    private cdr: ChangeDetectorRef
  ){

  }

  ngOnInit() {
    this.fetchProducts();
  }

  /**
   * fetchProducts() :Product[]
   */
  public fetchProducts() :void{
    
    this.productApi.getProducts().subscribe({
      next: (data: any)=>{
        
        this.products = data;
        this.isLoading = false;

        this.products.forEach((product)=>{
             if (product.stock <= 0) {
                product.status = 'Out of Stock';
              } 
              else if (product.stock <= 5) {
                product.status = 'Low Stock';
              } 
              else {
                product.status = 'Available';
              }
        });
        this.cdr.detectChanges();
      },
      error : (error)=>{
        this.isLoading = false;
        console.error('Error loading products:', error);
        this.errorMessage = 'Failed to load products. Please try again.';
        this.cdr.detectChanges();
       
      }
      
    });

  }

  handleToolbarClick(event: MouseEvent): void {
    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }

    const addButton = target.closest('button.add-btn');
    if (addButton?.textContent?.includes('Add Product')) {
      this.isCreatingProduct.set(true);
    }
  }

  closeCreateProduct(): void {
    this.isCreatingProduct.set(false);
  }

  onProductCreated(): void {
    this.isCreatingProduct.set(false);
    this.fetchProducts();
  }
}
