import { Component, inject, output, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ProductApi } from '../../../services/product-api';

@Component({
  imports: [ReactiveFormsModule],
  selector: 'app-create-product',
  styleUrl: './create-product.css',
  templateUrl: './create-product.html',
})
export class CreateProduct {
  private readonly formBuilder = inject(FormBuilder);
  private readonly productApi = inject(ProductApi);

  readonly created = output<void>();
  readonly cancelled = output<void>();
  readonly isSubmitting = signal(false);
  readonly errorMessage = signal('');

  readonly form = this.formBuilder.nonNullable.group({
    name: ['', [Validators.required, Validators.pattern(/\S/)]],
    sku: ['', [Validators.required, Validators.pattern(/\S/)]],
    categoryId: [0, [Validators.required, Validators.min(1), Validators.pattern(/^\d+$/)]],
    price: [0, [Validators.required, Validators.min(0)]],
    stock: [0, [Validators.required, Validators.min(0), Validators.pattern(/^\d+$/)]],
  });

  submit(): void {
    if (this.isSubmitting()) {
      return;
    }

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const values = this.form.getRawValue();
    this.isSubmitting.set(true);
    this.errorMessage.set('');

    this.productApi.createProduct({
      name: values.name.trim(),
      sku: values.sku.trim(),
      price: values.price,
      stock: values.stock,
      category: { id: values.categoryId },
    }).subscribe({
      next: () => {
        this.isSubmitting.set(false);
        this.created.emit();
      },
      error: (error: unknown) => {
        console.error('Error creating product:', error);
        this.isSubmitting.set(false);
        this.errorMessage.set('Failed to create product. Please try again.');
      },
    });
  }
}
