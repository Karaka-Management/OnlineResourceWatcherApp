1. Backend create invoice template
2. Api to create customer if it doesn't exist
3. Api to create a scheduled AND recurring invoice for customer
4. Cron to create invoices scheduled invoices
5. Api to adjust (delete, postpone, modify) scheduled invoice
6. Api to interact with stripe
7. Create billing and payment workflow (e.g. pre billing job = credit card charging)
8. Api to check invoice charging status (to potentially cancel service)

Example Workflow:

1. Get all scheduled invoices
2. Run pre job = credit card charging
3. Success: Create invoice
4. Success: Send invoice via email
5. Failure: Send Reminder/Info if new customer + cancel service?