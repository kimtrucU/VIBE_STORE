import { db } from './index.ts';
import { users, orders, wishlist } from './schema.ts';
import { eq, and } from 'drizzle-orm';

// Get or create user
export async function getOrCreateUser(uid: string, email: string, displayName?: string) {
  try {
    const result = await db.insert(users)
      .values({
        uid,
        email,
        displayName: displayName || null,
      })
      .onConflictDoUpdate({
        target: users.uid,
        set: {
          email,
          displayName: displayName || null,
        },
      })
      .returning();

    return result[0];
  } catch (error) {
    console.error('getOrCreateUser error:', error);
    throw new Error('Failed to synchronize user in database', { cause: error });
  }
}

// Get user profile
export async function getUserProfile(uid: string) {
  try {
    const result = await db.select()
      .from(users)
      .where(eq(users.uid, uid));
    return result[0] || null;
  } catch (error) {
    console.error('getUserProfile error:', error);
    throw new Error('Failed to fetch user profile', { cause: error });
  }
}

// Save Order
export async function createOrder(uid: string, items: string, customerInfo: string, paymentMethod: string, totalAmount: number, date: string) {
  try {
    // Resolve user internal id first
    const userRecord = await getUserProfile(uid);
    if (!userRecord) {
      throw new Error('User record not found in database');
    }

    const result = await db.insert(orders)
      .values({
        userId: userRecord.id,
        items,
        customerInfo,
        paymentMethod,
        totalAmount,
        date,
      })
      .returning();

    return result[0];
  } catch (error) {
    console.error('createOrder error:', error);
    throw new Error('Failed to create order', { cause: error });
  }
}

// Get User Orders
export async function getUserOrders(uid: string) {
  try {
    const userRecord = await getUserProfile(uid);
    if (!userRecord) {
      return [];
    }

    const result = await db.select()
      .from(orders)
      .where(eq(orders.userId, userRecord.id));

    return result;
  } catch (error) {
    console.error('getUserOrders error:', error);
    throw new Error('Failed to fetch user orders', { cause: error });
  }
}

// Get User Wishlist
export async function getUserWishlist(uid: string) {
  try {
    const userRecord = await getUserProfile(uid);
    if (!userRecord) {
      return [];
    }

    const result = await db.select()
      .from(wishlist)
      .where(eq(wishlist.userId, userRecord.id));

    return result.map(item => item.productId);
  } catch (error) {
    console.error('getUserWishlist error:', error);
    throw new Error('Failed to fetch user wishlist', { cause: error });
  }
}

// Add to Wishlist
export async function addToWishlist(uid: string, productId: string) {
  try {
    const userRecord = await getUserProfile(uid);
    if (!userRecord) {
      throw new Error('User record not found in database');
    }

    const result = await db.insert(wishlist)
      .values({
        userId: userRecord.id,
        productId,
      })
      .returning();

    return result[0];
  } catch (error) {
    console.error('addToWishlist error:', error);
    throw new Error('Failed to add item to wishlist', { cause: error });
  }
}

// Remove from Wishlist
export async function removeFromWishlist(uid: string, productId: string) {
  try {
    const userRecord = await getUserProfile(uid);
    if (!userRecord) {
      throw new Error('User record not found in database');
    }

    await db.delete(wishlist)
      .where(and(
        eq(wishlist.userId, userRecord.id),
        eq(wishlist.productId, productId)
      ));

    return true;
  } catch (error) {
    console.error('removeFromWishlist error:', error);
    throw new Error('Failed to remove item from wishlist', { cause: error });
  }
}
