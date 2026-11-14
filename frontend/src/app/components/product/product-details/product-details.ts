import { Component , Input , Output, EventEmitter} from '@angular/core';

@Component({
  imports: [],
  selector: 'app-product-details',
  styleUrl: './product-details.css',
  templateUrl: './product-details.html',
})
export class ProductDetails {

  selectedProduct: any = null;

  
    @Input() product: any = null;
    @Output() close = new EventEmitter<void>();

    closeProductDetails(): void {
      this.close.emit();
  }

}
