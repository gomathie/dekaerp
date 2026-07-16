import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";

test.describe("Purchase Flow E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
    });

    test("Purchase Flow - RFQ To Purchase Order", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E Purchase Vendor ${key}`;
        const productName = `E2E Purchase Product ${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "145",
        });

        await purchasePage.createQuotation({
            vendorName,
            productName,
            quantity: "2",
            unitPrice: "145",
        });

        await purchasePage.confirmCurrentQuotation();
        await purchasePage.expectPurchaseOrderVisible(vendorName);
    });

    test("Purchase Flow - Confirmed Agreement To Purchase Order", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E Blanket Vendor ${key}`;
        const productName = `E2E Blanket Product ${key}`;

        await purchasePage.setPurchaseAgreementsEnabled(true);

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.blanket.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "210",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "8",
            unitPrice: "210",
            reference: `E2E-BLANKET-${key}`,
        });

        await purchasePage.confirmCurrentPurchaseAgreement();

        await purchasePage.createQuotationFromAgreement({
            vendorName,
            quantity: "3",
        });

        await purchasePage.confirmCurrentQuotation();
        await purchasePage.expectPurchaseOrderVisible(vendorName);
    });
});
